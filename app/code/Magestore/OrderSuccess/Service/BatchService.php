<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Service;

use Magestore\OrderSuccess\Api\Data\BatchInterface;

/**
 * Class BatchService
 * @package Magestore\OrderSuccess\Service
 */
class BatchService
{
    /**
     * @var \Magestore\OrderSuccess\Api\BatchRepositoryInterface
     */
    protected $batchRepository;
    
    /**
     * @var \Magestore\OrderSuccess\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * BatchService constructor.
     * @param \Magestore\OrderSuccess\Api\BatchRepositoryInterface $batchRepository
     * @param \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magestore\OrderSuccess\Api\BatchRepositoryInterface $batchRepository,
        \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository
        )
    {
        $this->batchRepository = $batchRepository;
        $this->orderRepository = $orderRepository;
    } 
    
    /**
     * Add Sales to Batch
     * 
     * @param BatchInterface $batch
     * @param int $orderId
     */
    public function addOrderToBatch(BatchInterface $batch, $orderId)
    {
        $order = $this->orderRepository->getById($orderId);
        if($order->getId()) {
            $order->setBatchId($batch->getId());
            $this->orderRepository->save($order);
        }
        return $batch;
    }
    
    /**
     * Add Orders to Batch
     * 
     * @param BatchInterface $batch
     * @param array $orderIds
     */
    public function addOrdersToBatch(BatchInterface $batch, $orderIds)
    {
        $this->orderRepository->massUpdateBatch($orderIds, $batch->getId());
        return $this;
    }
    
    /**
     * Remove Order from Batch
     *
     * @param int $orderId
     */
    public function removeOrderFromBatch($orderId)
    {
        $order = $this->orderRepository->getById($orderId);
        if($order->getId()) {
            $order->setBatchId(0);
            $this->orderRepository->save($order);
        }        
        return $this;
    }
    
    /**
     * Remove Orders from Batch
     *
     * @param array $orderIds
     */
    public function removeOrdersFromBatch($orderIds)
    {
        $this->orderRepository->massUpdateBatch($orderIds, null);
        return $this;
    }

    /**
     * Cancel Batch
     *
     * @param array $orderIds
     */
    public function cancelBatchs($batchIds)
    {
        $order = $this->orderRepository->getOrderListFromBatch($batchIds);
        $orderIds = $order->getAllIds();
        $this->orderRepository->massUpdateBatch($orderIds, null);
        $this->batchRepository->massDelete($batchIds);
        return $this;
    }
}