<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Repository\PackRequest;

use Magestore\FulfilSuccess\Api\PackRequestOrderItemRepositoryInterface;

class PackRequestOrderItemRepository extends \Magento\Sales\Model\Order\ItemRepository implements PackRequestOrderItemRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getNeedToPackItemsCollection($packRequestId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
        $itemCollection = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Item\Collection');
        $itemCollection->getSelect()
            ->join(
                ['packrequest_item' => $itemCollection->getTable('os_fulfilsuccess_packrequest_item')],
                'main_table.item_id = packrequest_item.item_id',
                ['*']
            )
            ->where("packrequest_item.request_qty > packrequest_item.packed_qty");

        $itemCollection
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'packrequest_item.pack_request_id',
                $packRequestId
            )->load();

        return $itemCollection;
    }

    /**
     * @inheritDoc
     */
    public function getPackedItemsCollection($packRequestId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
        $itemCollection = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Item\Collection');
        $itemCollection->getSelect()
            ->join(
                ['packrequest_item' => $itemCollection->getTable('os_fulfilsuccess_packrequest_item')],
                'main_table.item_id = packrequest_item.item_id',
                ['*']
            )
            ->where("packrequest_item.request_qty = packrequest_item.packed_qty");

        $itemCollection
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'packrequest_item.pack_request_id',
                $packRequestId
            )->load();

        return $itemCollection;
    }

}
