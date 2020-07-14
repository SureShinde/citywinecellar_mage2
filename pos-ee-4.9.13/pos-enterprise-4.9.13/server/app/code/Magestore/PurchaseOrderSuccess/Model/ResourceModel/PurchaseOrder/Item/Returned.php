<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item;

/**
 * Class Returned
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item
 */
class Returned extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\AbstractResource
{
    const TABLE_PURCHASE_ORDER_ITEM_RETURNED = 'os_purchase_order_item_returned';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_ITEM_RETURNED, 'purchase_order_item_returned_id');
    }
}