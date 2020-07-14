<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item;

/**
 * Class Received
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item
 */
class Received extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\AbstractResource
{
    const TABLE_PURCHASE_ORDER_ITEM_RECEIVED = 'os_purchase_order_item_received';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_ITEM_RECEIVED, 'purchase_order_item_received_id');
    }
}