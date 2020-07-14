<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PaymentInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_INVOICE_PAYMENT_ID = 'purchase_order_invoice_payment_id';
    
    const PURCHASE_ORDER_INVOICE_ID = 'purchase_order_invoice_id';
    
    const PAYMENT_AT = 'payment_at';
    
    const PAYMENT_METHOD = 'payment_method';
    
    const PAYMENT_AMOUNT = 'payment_amount';
    
    const DESCRIPTION = 'description';
    
    const CREATED_AT = 'created_at';
    
    /**#@-*/

    /**
     * Get purchase order invoice payment id
     *
     * @return int
     */
    public function getPurchaseOrderInvoicePaymentId();

    /**
     * Set purchase order invoice payment id
     *
     * @param int $purchaseOrderInvoicePaymentId
     * @return $this
     */
    public function setPurchaseOrderInvoicePaymentId($purchaseOrderInvoicePaymentId);

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
     * Get payment method
     *
     * @return string
     */
    public function getPaymentMethod();

    /**
     * Set payment method
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Get payment amount
     *
     * @return float
     */
    public function getPaymentAmount();

    /**
     * Set payment amount
     *
     * @param float $paymentAmount
     * @return $this
     */
    public function setPaymentAmount($paymentAmount);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get payment at
     *
     * @return string
     */
    public function getPaymentAt();

    /**
     * Set payment at
     *
     * @param string $paymentAt
     * @return $this
     */
    public function setPaymentAt($paymentAt);

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