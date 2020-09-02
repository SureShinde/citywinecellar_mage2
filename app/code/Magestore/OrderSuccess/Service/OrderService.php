<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Service;
use Magestore\OrderSuccess\Service\BatchService;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class OrderService
 * @package Magestore\OrderSuccess\Service
 */
class OrderService
{
    const KEEP_BATCH_AFTER_VERIFIED_XML_CONFIG = 'ordersuccess/order/keep_verify_batch';
    
    /**
     * @var \Magestore\OrderSuccess\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magestore\OrderSuccess\Api\BatchRepositoryInterface
     */
    protected $batchRepository;
    
    /**
     * @var BatchService
     */
    protected $batchService;
    
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * OrderService constructor.
     * @param \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository
     * @param \Magestore\OrderSuccess\Api\BatchRepositoryInterface $batchRepository
     */
    public function __construct(
        \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository,
        \Magestore\OrderSuccess\Api\BatchRepositoryInterface $batchRepository,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        BatchService $batchService,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->orderRepository = $orderRepository;
        $this->batchRepository = $batchRepository;
        $this->batchService = $batchService;
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Veriry order
     *
     * @param int $orderId
     */
    public function verifyOrder($orderId)
    {
        $this->changeVerifyState($orderId, 1);
    }

    /**
     * Move order to verify step
     *
     * @param array $orderId
     */
    public function moveOrderToVerify($orderId)
    {
        $this->changeVerifyState($orderId, 0);
    }

    /**
     * Change verify state
     *
     * @param int $orderId
     * @param int $state
     *
     * @return $this
     */
    public function changeVerifyState($orderId, $state)
    {
        $order = $this->orderRepository->getById($orderId);
        if($order->getId()) {
            $order->setIsVerified($state);
            $this->orderRepository->save($order);
            if(!$this->scopeConfig->getValue(self::KEEP_BATCH_AFTER_VERIFIED_XML_CONFIG)) {
                $this->batchService->removeOrderFromBatch($order->getId());
            }
        }
        return $this;
    }

    /**
     * Verify orders
     *
     * @param array $orderIds
     * @return $this
     */
    public function verifyOrders($orderIds)
    {
        $this->orderRepository->massVerify($orderIds, 1);
        if(!$this->scopeConfig->getValue(self::KEEP_BATCH_AFTER_VERIFIED_XML_CONFIG)) {
            $this->batchService->removeOrdersFromBatch($orderIds);
        }        
        return $this;
    }

    /**
     * Move orders to verify step
     *
     * @param array $orderIds
     * @return $this
     */
    public function moveOrdersToVerify($orderIds)
    {
        $this->orderRepository->massVerify($orderIds, 0);
        if(!$this->scopeConfig->getValue(self::KEEP_BATCH_AFTER_VERIFIED_XML_CONFIG)) {
            $this->batchService->removeOrdersFromBatch($orderIds);
        }          
        return $this;
    }

    /**
     * Move orders to verify step
     *
     * @param array $orderIds
     * @return $this
     */
    public function getAllBatchIds($collection)
    {
        $ids = [];
        foreach ($collection->getItems() as $item) {
            $ids[] = $item->getBatchId();
        }
        return $ids;
    }

    /**
     * Add New Data for Sales
     *
     * @param array $data
     * @param int $orderId
     * @return $this
     */
    public function addNewDataForOrder($data, $orderId)
    {
        $order = $this->orderRepository->getById($orderId);
        if($order->getId()) {
            if(isset($data['batch_id']) && $data['batch_id'] !='na') {
                if($data['batch_id'] == 'remove'){
                    $data['batch_id'] = 0;
                }
                if($data['batch_id'] == 'newbatch'){
                    $batch = $this->batchRepository->newBatch();
                    $data['batch_id'] = $batch->getId();
                }
                $order->setBatchId($data['batch_id']);
            }
            if(isset($data['tag_color']) &&  $data['tag_color'] != 'na'
                && strpos($order->getTagColor(), $data['tag_color']) === false) {
                if($data['tag_color'] == 'remove'){
                    $data['tag_color'] = '';
                }else if($order->getTagColor() != '' && $order->getTagColor() != null){
                    $data['tag_color'] = $order->getTagColor() . ',' . $data['tag_color'];
                }
                $order->setTagColor($data['tag_color']);
            }
            $this->orderRepository->save($order);
            if(isset($data['note'])) {
                $orderWithComment = $this->collectionFactory->create()->addFieldToFilter('entity_id', $order->getId())
                    ->getFirstItem();
                if ($data['note'] != $orderWithComment->getNote()){
                    $history = $order->addStatusHistoryComment($data['note'], $order->getStatus());
                    $history->save();
                }
            }
        }
        return $this;
    }
}