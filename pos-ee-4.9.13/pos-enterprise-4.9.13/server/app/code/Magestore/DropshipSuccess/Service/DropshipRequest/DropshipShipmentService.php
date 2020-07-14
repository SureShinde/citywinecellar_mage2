<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service\DropshipRequest;

use Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface;

/**
 * Class DropshipShipmentService
 * @package Magestore\DropshipSuccess\Service\DropshipRequest
 */
class DropshipShipmentService
{
    /**
     * @var DropshipRequestItemService
     */
    protected $dropshipRequestItemService;

    /**
     * @var \Magestore\DropshipSuccess\Api\DropshipRequestItemRepositoryInterface
     */
    protected $dropshipRequestItemRepository;

    /**
     * @var \Magestore\DropshipSuccess\Api\DropshipShipmentRepositoryInterface
     */
    protected $dropshipShipmentRepository;

    /**
     * @var \Magestore\DropshipSuccess\Api\DropshipShipmentItemRepositoryInterface
     */
    protected $dropshipShipmentItemRepository;

    /**
     * @var \Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment\ItemFactory
     */
    protected $dropshipShipmentItemFactory;


    /**
     * DropshipShipmentService constructor.
     * @param DropshipRequestItemService $dropshipRequestItemService
     * @param \Magestore\DropshipSuccess\Api\DropshipRequestItemRepositoryInterface $dropshipRequestItemRepository
     * @param \Magestore\DropshipSuccess\Api\DropshipShipmentRepositoryInterface $dropshipShipmentRepository
     * @param \Magestore\DropshipSuccess\Api\DropshipShipmentItemRepositoryInterface $dropshipShipmentItemRepository
     * @param \Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment\ItemFactory $dropshipShipmentItemFactory
     */
    public function __construct(
        DropshipRequestItemService $dropshipRequestItemService,
        \Magestore\DropshipSuccess\Api\DropshipRequestItemRepositoryInterface $dropshipRequestItemRepository,
        \Magestore\DropshipSuccess\Api\DropshipShipmentRepositoryInterface $dropshipShipmentRepository,
        \Magestore\DropshipSuccess\Api\DropshipShipmentItemRepositoryInterface $dropshipShipmentItemRepository,
        \Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment\ItemFactory $dropshipShipmentItemFactory
    ) {
        $this->dropshipRequestItemService = $dropshipRequestItemService;
        $this->dropshipRequestItemRepository = $dropshipRequestItemRepository;
        $this->dropshipShipmentRepository = $dropshipShipmentRepository;
        $this->dropshipShipmentItemRepository = $dropshipShipmentItemRepository;
        $this->dropshipShipmentItemFactory = $dropshipShipmentItemFactory;
    }

    /**
     * Validate post data
     * 
     * @param array $postData
     * @return array
     */
    public function validateShipmentData($postData = []){
        $items = $this->dropshipRequestItemService
            ->getItemsInDropship($postData['dropship_request_id'])
            ->getCanShipItem();
        $postItems = [];
        /**
         * @var \Magestore\DropshipSuccess\Model\DropshipRequest\Item $item
         */
        foreach ($items as $item){
            if(isset($postData['shipment']['items'][$item->getItemId()])){
                $postItem = $postData['shipment']['items'][$item->getItemId()];
                $maxQty = $item->getQtyRequested() - $item->getQtyShipped() - $item->getQtyCanceled();
                $postItem = min((float)$postItem, $maxQty);
                $postItems[$item->getItemId()] = $postItem;
            }
        }
        $postData['shipment']['items'] = $postItems;
        $postData['total_shipped'] = array_sum($postItems);
        return $postData;
    }

    /**
     * @param DropshipShipmentInterface $dropshipShipment
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     */
    public function createDropshipItem(
        DropshipShipmentInterface $dropshipShipment, \Magento\Sales\Model\Order\Shipment $shipment
    ){
        $shipmentItem = $shipment->getItemsCollection();
        /**
         * @var \Magento\Sales\Api\Data\ShipmentItemInterface $item
         */
        foreach ($shipmentItem as $item) {
            $dropshipShipmentItem = $this->dropshipShipmentItemFactory->create();
            $dropshipShipmentItem->setDropshipShipmentId($dropshipShipment->getDropshipShipmentId());
            $dropshipShipmentItem->setItemId($item->getEntityId());
            $dropshipShipmentItem->setItemSku($item->getSku());
            $dropshipShipmentItem->setItemName($item->getName());
            $dropshipShipmentItem->setQtyShipped($item->getQty());
            $dropshipShipmentItem->setId(null);
            $this->dropshipShipmentItemRepository->save($dropshipShipmentItem);
            $dropshipItem = $this->dropshipRequestItemService
                ->getItemsInDropship($dropshipShipment->getDropshipRequestId())
                ->addFieldToFilter('item_id', $item->getOrderItemId())
                ->getFirstItem();
            if($dropshipItem->getId()){
                $dropshipItem->setQtyShipped($dropshipItem->getQtyShipped()+$item->getQty());
                $this->dropshipRequestItemRepository->save($dropshipItem);
            }
        }
    }
}