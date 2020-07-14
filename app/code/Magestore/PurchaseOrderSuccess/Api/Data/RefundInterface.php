<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface RefundInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_INVOICE_REFUND_ID = 'purchase_order_invoice_refund_id';
    
    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';
    
    const REFUND_AMOUNT = 'refund_amount';
    
    const REASON = 'reason';

    const REFUND_AT = 'refund_at';
    
    const CREATED_AT = 'created_at';
    
    /**#@-*/

    /**
     * Get purchase order invoice refund id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceRefundId();

    /**
     * Set purchase order invoice refund id
     *
     * @param int $purchaseOrderInvoiceRefundId
     * @return $this
     */
    public function setPurchaseOrderInvoiceRefundId($purchaseOrderInvoiceRefundId);

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
     * Get refund amount
     *
     * @return float
     */
    public function getRefundAmount();

    /**
     * Set refund amount
     *
     * @param float $refundAmount
     * @return $this
     */
    public function setRefundAmount($refundAmount);

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason();

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason);

    /**
     * Get refund at
     *
     * @return string
     */
    public function getRefundAt();

    /**
     * Set refund at
     *
     * @param string $refundAt
     * @return $this
     */
    public function setRefundAt($refundAt);

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