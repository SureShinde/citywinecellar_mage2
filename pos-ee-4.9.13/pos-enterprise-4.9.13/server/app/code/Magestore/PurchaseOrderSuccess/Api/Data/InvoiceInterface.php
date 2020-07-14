<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface InvoiceInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';

    const PURCHASE_ORDER_ID = 'purchase_order_id';

    const INVOICE_CODE = 'invoice_code';

    const BILLED_AT = 'billed_at';
    
    const TOTAL_QTY_BILLED = 'total_qty_billed';

    const SUBTOTAL = 'subtotal';

    const TOTAL_TAX = 'total_tax';

    const TOTAL_DISCOUNT = 'total_discount';

    const GRAND_TOTAL_EXCL_TAX = 'grand_total_excl_tax';
    
    const GRAND_TOTAL_INCL_TAX = 'grand_total_incl_tax';

    const TOTAL_DUE = 'total_due';

    const TOTAL_REFUND = 'total_refund';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const CODE_LENGTH = 8;

    /**#@-*/

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
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId();

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId);

    /**
     * Get invoice code
     *
     * @return string
     */
    public function getInvoiceCode();

    /**
     * Set invoice code
     *
     * @param string $invoiceCode
     * @return $this
     */
    public function setInvoiceCode($invoiceCode);

    /**
     * Get billed at
     *
     * @return string
     */
    public function getBilledAt();

    /**
     * Set billed at
     *
     * @param string $billedAt
     * @return $this
     */
    public function setBilledAt($billedAt);

    /**
     * Get total qty billed
     *
     * @return float
     */
    public function getTotalQtyBilled();

    /**
     * Set total qty billed
     *
     * @param float $totalQtyBilled
     * @return $this
     */
    public function setTotalQtyBilled($totalQtyBilled);

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal();

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal);

    /**
     * Get total tax
     *
     * @return float
     */
    public function getTotalTax();

    /**
     * Set total tax
     *
     * @param float $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax);

    /**
     * Get total discount
     *
     * @return float
     */
    public function getTotalDiscount();

    /**
     * Set total discount
     *
     * @param float $totalDiscount
     * @return $this
     */
    public function setTotalDiscount($totalDiscount);

    /**
     * Get grand total exclude tax
     *
     * @return float
     */
    public function getGrandTotalExclTax();

    /**
     * Set grand total exclude tax
     *
     * @param float $grandTotalExclTax
     * @return $this
     */
    public function setGrandTotalExclTax($grandTotalExclTax);

    /**
     * Get grand total include tax
     *
     * @return float
     */
    public function getGrandTotalInclTax();

    /**
     * Set grand total include tax
     *
     * @param float $grandTotalInclTax
     * @return $this
     */
    public function setGrandTotalInclTax($grandTotalInclTax);

    /**
     * Get total due
     *
     * @return float
     */
    public function getTotalDue();

    /**
     * Set total due
     *
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue);

    /**
     * Get total refund
     *
     * @return float
     */
    public function getTotalRefund();

    /**
     * Set total refund
     *
     * @param float $totalRefund
     * @return $this
     */
    public function setTotalRefund($totalRefund);

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

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}