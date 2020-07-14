<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice;

/**
 * Class Refund
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice
 */
class Refund extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\AbstractResource
{
    const TABLE_PURCHASE_ORDER_INVOICE_REFUND = 'os_purchase_order_invoice_refund';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_INVOICE_REFUND, 'purchase_order_invoice_refund_id');
    }
}