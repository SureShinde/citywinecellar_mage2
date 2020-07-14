<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface InvoiceItemInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_INVOICE_ITEM_ID = 'purchase_order_invoice_item_id';
    
    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';
    
    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';
    
    const QTY_BILLED = 'qty_billed';
    
    const UNIT_PRICE = 'unit_price';
    
    const TAX = 'tax';
    
    const DISCOUNT = 'discount';
    
    const CREATED_AT = 'created_at';

    /**#@-*/

    /**
     * Get purchase order invoice item id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceItemId();

    /**
     * Set purchase order invoice item id
     *
     * @param int $purchaseOrderInvoiceItemId
     * @return $this
     */
    public function setPurchaseOrderInvoiceItemId($purchaseOrderInvoiceItemId);

    /**
     * Get purchase order invoice id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceId();

    /**
     * Set purchase order invoice id
     *
     * @param int $purchaseOrderInvoiceId
     * @return $this
     */
    public function setPurchaseOrderInvoiceId($purchaseOrderInvoiceId);

    /**
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId();

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId);

    /**
     * Get qty billed
     *
     * @return float
     */
    public function getQtyBilled();

    /**
     * Set qty billed
     *
     * @param float $qtyBilled
     * @return $this
     */
    public function setQtyBilled($qtyBilled);

    /**
     * Get unit price
     *
     * @return float
     */
    public function getUnitPrice();

    /**
     * Set unit price
     *
     * @param float $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice);

    /**
     * Get tax
     *
     * @return float
     */
    public function getTax();

    /**
     * Set tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax($tax);

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount();

    /**
     * Set discount
     *
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}