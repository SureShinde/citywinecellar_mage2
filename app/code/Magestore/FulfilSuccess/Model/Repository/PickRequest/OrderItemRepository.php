<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\PickRequest;

use Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface;
use Magestore\OrderSuccess\Api\Data\OrderItemInterface as OrderSuccessOrderItemInterface;

class OrderItemRepository extends \Magento\Sales\Model\Order\ItemRepository implements OrderItemRepositoryInterface
{
    /**
     * Mass Update PrepareShip Qty of Sales Items
     *
     * @param array $items
     */
    public function massUpdatePrepareShipQty($items)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceOrderItem = $objectManager->get('Magento\Sales\Model\ResourceModel\Order\Item');
        foreach ($items as $itemId => $qtyChange) {
            $qtyPrepareShipChange = isset($qtyChange[OrderSuccessOrderItemInterface::QTY_PREPARESHIP]) ?
                $qtyChange[OrderSuccessOrderItemInterface::QTY_PREPARESHIP] :
                0;

            if (!$qtyPrepareShipChange) {
                continue;
            }

            $orderItem = $objectManager->create('Magento\Sales\Model\Order\Item');
            $resourceOrderItem->load($orderItem, $itemId);

            /* ignore child of ship-together bundle item */
            if ($orderItem->getParentItem()
                && $orderItem->getParentItem()->getProductType() != \Magento\Bundle\Model\Product\Type::TYPE_CODE
                && !$orderItem->isShipSeparately()
            ) {
                continue;
            }

            if (!$orderItem->getSku()) {
                continue;
            }

            $qtyPrepareShip = $orderItem->getData(OrderSuccessOrderItemInterface::QTY_PREPARESHIP);
            $qtyPrepareShip = max(0, ($qtyPrepareShip + $qtyPrepareShipChange));
            $qtyPrepareShip = min($qtyPrepareShip, $orderItem->getQtyToShip());

            $orderItem->setData(OrderSuccessOrderItemInterface::QTY_PREPARESHIP, $qtyPrepareShip);

            $this->metadata->getMapper()->save($orderItem);
            if ($orderItem->getParentItemId()) {
                $parentItem = $objectManager->create('Magento\Sales\Model\Order\Item');
                $resourceOrderItem->load($parentItem, $orderItem->getParentItemId());
                if ($parentItem->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $parentItem->setData(OrderSuccessOrderItemInterface::QTY_PREPARESHIP, $qtyPrepareShip);
                    $this->metadata->getMapper()->save($parentItem);
                }
            }
        }

    }


    /**
     * Retrieve order items collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getItemsCollection($pickRequestId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
        $itemCollection = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Item\Collection');
        $itemCollection->getSelect()
            ->join(
                ['pickrequest_item' => $itemCollection->getTable('os_fulfilsuccess_pickrequest_item')],
                'main_table.item_id = pickrequest_item.item_id',
                ['*']
            )->where("pickrequest_item.request_qty > pickrequest_item.picked_qty");

        $itemCollection
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'pickrequest_item.pick_request_id',
                $pickRequestId
            )->load();

        return $itemCollection;
    }

    /**
     * Retrieve order items collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getPickedItemsCollection($pickRequestId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
        $itemCollection = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Item\Collection');
        $itemCollection->getSelect()
            ->join(
                ['pickrequest_item' => $itemCollection->getTable('os_fulfilsuccess_pickrequest_item')],
                'main_table.item_id = pickrequest_item.item_id',
                ['*']
            )
            //->where("pickrequest_item.request_qty = pickrequest_item.picked_qty")
            ->where("pickrequest_item.picked_qty > 0");

        $itemCollection
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'pickrequest_item.pick_request_id',
                $pickRequestId
            )->load();

        return $itemCollection;
    }

}
