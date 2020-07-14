<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice;

/**
 * Class Item
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice
 */
class Item extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\AbstractResource
{
    const TABLE_PURCHASE_ORDER_INVOICE_ITEM = 'os_purchase_order_invoice_item';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_INVOICE_ITEM, 'purchase_order_invoice_item_id');
    }
}