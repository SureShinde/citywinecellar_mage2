<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item;

/**
 * Class Transferred
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item
 */
class Transferred extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\AbstractResource
{
    const TABLE_PURCHASE_ORDER_ITEM_TRANSFERRED = 'os_purchase_order_item_transferred';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_ITEM_TRANSFERRED, 'purchase_order_item_transferred_id');
    }
}