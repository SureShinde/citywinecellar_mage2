<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PickRequest;

use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;
use Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemInterfaceFactory;

class ItemService
{
    
    /**
     * @var OrderItemRepositoryInterface 
     */
    protected $orderItemRepository;
    
    /**
     * @var OrderItemInterfaceFactory 
     */
    protected $orderItemFactory;
    
    public function __construct(OrderItemRepositoryInterface $orderItemRepository,
            OrderItemInterfaceFactory $orderItemFactory)
    {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemFactory = $orderItemFactory;
    }
    
    /**
     * Get need-to-ship qty of Sales Item
     * 
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return float
     */
    public function getQtyToShip($item)
    {
        $qtyToShip = $item->getQtyOrdered() - $item->getQtyShipped() - $item->getQtyRefunded() - $item->getQtyCanceled();
        return max($qtyToShip, 0);
    }
    
    /**
     * Update qty_prepareship in Sales Item
     * 
     * @param \Magestore\OrderSuccess\Api\Data\OrderItemInterface $item
     * @param float $changeQty
     */
    public function updatePrepareShipQty($item, $changeQty)
    {
        $qtyPrepareShip = max(0, $item->getQtyPrepareship() + $changeQty);
        $qtyPrepareShip = min($qtyPrepareShip, $this->getQtyToShip($item));
        $item->setQtyPrepareship($qtyPrepareShip);
        /* prepare OrderItem to save */
        $orderItem = $this->orderItemFactory->create();
        $orderItem->setData($item->getData());
        $this->orderItemRepository->save($orderItem);

        //$orderItem = $this->orderItemRepository->get($orderItem->getId());
        if($item->getParentItemId()){
            $parent = $this->orderItemRepository->get($item->getParentItemId());      
            if($parent && $parent->getId()){
                if($parent->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){     
                    $qtyPrepareShip = min($qtyPrepareShip, $this->getQtyToShip($parent));
                    $parent->setQtyPrepareship($qtyPrepareShip);
                    $this->orderItemRepository->save($parent);
                }
            }
        }
    }
    
    /**
     * Filter qty data before updating to Pick Request Item
     * 
     * @param array $qtys
     * @return array
     */
    public function filterQtyData($qtys)
    {
        $filteredQtys = [];
        if(!empty($qtys)) {
            foreach($qtys as $qtyData) {
                $filteredQty = [];
                if(isset($qtyData[PickRequestItemInterface::ITEM_ID])) {
                    $filteredQty[PickRequestItemInterface::ITEM_ID] = $qtyData[PickRequestItemInterface::ITEM_ID];
                }
                if(isset($qtyData[PickRequestItemInterface::REQUEST_QTY])) {
                    $filteredQty[PickRequestItemInterface::REQUEST_QTY] = $qtyData[PickRequestItemInterface::REQUEST_QTY];
                }
                if(isset($qtyData[PickRequestItemInterface::PICKED_QTY])) {
                    $filteredQty[PickRequestItemInterface::PICKED_QTY] = $qtyData[PickRequestItemInterface::PICKED_QTY];
                }    
                if(!empty($filteredQty)) {
                    $filteredQtys[] = $filteredQty;
                }
            }
        }
        return $filteredQtys;
    }

}
