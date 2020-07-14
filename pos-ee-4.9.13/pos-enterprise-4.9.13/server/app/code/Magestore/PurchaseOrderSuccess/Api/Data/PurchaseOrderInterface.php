<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PurchaseOrderInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_ID = 'purchase_order_id';

    const PURCHASE_CODE = 'purchase_code';

    const SUPPLIER_ID = 'supplier_id';

    const TYPE = 'type';

    const STATUS = 'status';

    const SEND_EMAIL = 'send_email';

    const IS_SENT = 'is_sent';

    const COMMENT = 'comment';

    const SHIPPING_ADDRESS = 'shipping_address';

    const SHIPPING_METHOD = 'shipping_method';

    const SHIPPING_COST = 'shipping_cost';

    const PAYMENT_TERM = 'payment_term';

    const PLACED_VIA = 'placed_via';

    const USER_ID = 'user_id';

    const CREATED_BY = 'created_by';

    const TOTAL_QTY_ORDERRED = 'total_qty_orderred';
    
    const TOTAL_QTY_RECEIVED = 'total_qty_received';
    
    const TOTAL_QTY_BILLED = 'total_qty_billed';
    
    const TOTAL_QTY_TRANSFERRED = 'total_qty_transferred';
    
    const TOTAL_QTY_RETURNED = 'total_qty_returned';

    const SUBTOTAL = 'subtotal';

    const TOTAL_TAX = 'total_tax';

    const TOTAL_DISCOUNT = 'total_discount';

    const GRAND_TOTAL_EXCL_TAX = 'grand_total_excl_tax';

    const GRAND_TOTAL_INCL_TAX = 'grand_total_incl_tax';

    const TOTAL_BILLED = 'total_billed';

    const TOTAL_DUE = 'total_due';

    const CURRENCY_CODE = 'currency_code';

    const CURRENCY_RATE = 'currency_rate';

    const PURCHASED_AT = 'purchased_at';

    const STARTED_AT = 'started_at';

    const EXPECTED_AT = 'expected_at';

    const CANCELED_AT = 'canceled_at';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
    
    const ITEMS = 'items';

    const PURCHASE_KEY = 'purchase_key';

    /**#@-*/

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
     * Get purchase code
     *
     * @return string|null
     */
    public function getPurchaseCode();

    /**
     * Set purchase code
     *
     * @param string $purchaseCode
     * @return $this
     */
    public function setPurchaseCode($purchaseCode);

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getSupplierId();

    /**
     * Set supplier id
     *
     * @param int $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId);
    
    /**
     * Get type
     *
     * @return int
     */
    public function getType();

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type);
    
    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get send email
     *
     * @return int
     */
    public function getSendEmail();

    /**
     * Set send email
     *
     * @param int $sendEmail
     * @return $this
     */
    public function setSendEmail($sendEmail);

    /**
     * Get is sent email
     *
     * @return boolean
     */
    public function getIsSent();

    /**
     * Set is sent email
     *
     * @param int $isSent
     * @return $this
     */
    public function setIsSent($isSent);

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment);

    /**
     * Get shipping address
     *
     * @return string
     */
    public function getShippingAddress();

    /**
     * Set shipping address
     *
     * @param string $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Get shipping method
     *
     * @return string
     */
    public function getShippingMethod();

    /**
     * Set shipping method
     *
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod);

    /**
     * Get shipping cost
     *
     * @return float
     */
    public function getShippingCost();

    /**
     * Set shipping cost
     *
     * @param float $shippingCost
     * @return $this
     */
    public function setShippingCost($shippingCost);

    /**
     * Get payment term
     *
     * @return string
     */
    public function getPaymentTerm();

    /**
     * Set payment term
     *
     * @param string $paymentTerm
     * @return $this
     */
    public function setPaymentTerm($paymentTerm);

    /**
     * Get placed via
     *
     * @return string
     */
    public function getPlacedVia();

    /**
     * Set placed via
     *
     * @param string $placedVia
     * @return $this
     */
    public function setPlacedVia($placedVia);

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set user id
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy();

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);

    /**
     * Get total qty orderred
     *
     * @return float
     */
    public function getTotalQtyOrderred();

    /**
     * Set total qty orderred
     *
     * @param float $totalQtyOrderred
     * @return $this
     */
    public function setTotalQtyOrderred($totalQtyOrderred);

    /**
     * Get total qty received
     *
     * @return float
     */
    public function getTotalQtyReceived();

    /**
     * Set total qty received
     *
     * @param float $totalQtyReceived
     * @return $this
     */
    public function setTotalQtyReceived($totalQtyReceived);

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
     * Get total qty transferred
     *
     * @return float
     */
    public function getTotalQtyTransferred();

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyTransferred($totalQtyTransferred);

    /**
     * Get total qty returned
     *
     * @return float
     */
    public function getTotalQtyReturned();

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyReturned($totalQtyReturned);

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
     * Get total billed
     *
     * @return float
     */
    public function getTotalBilled();

    /**
     * Set total billed
     *
     * @param float $totalBilled
     * @return $this
     */
    public function setTotalBilled($totalBilled);

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
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode();

    /**
     * Set currency code
     *
     * @param string $currencyCode
     * @return $this
     */
    public function setCurrencyCode($currencyCode);

    /**
     * Get currency rate
     *
     * @return string
     */
    public function getCurrencyRate();

    /**
     * Set currency rate
     *
     * @param string $currencyRate
     * @return $this
     */
    public function setCurrencyRate($currencyRate);

    /**
     * Get purchased at
     *
     * @return string
     */
    public function getPurchasedAt();

    /**
     * Set purchased at
     *
     * @param string $purchasedAt
     * @return $this
     */
    public function setPurchasedAt($purchasedAt);

    /**
     * Get started at
     *
     * @return string
     */
    public function getStartedAt();

    /**
     * Set started at
     *
     * @param string $startedAt
     * @return $this
     */
    public function setStartedAt($startedAt);

    /**
     * Get expected at
     *
     * @return string
     */
    public function getExpectedAt();

    /**
     * Set expected at
     *
     * @param string $expectedAt
     * @return $this
     */
    public function setExpectedAt($expectedAt);

    /**
     * Get canceled at
     *
     * @return string
     */
    public function getCanceledAt();

    /**
     * Set canceled at
     *
     * @param string $canceledAt
     * @return $this
     */
    public function setCanceledAt($canceledAt);

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

    /**
     * Get purchaseKey
     *
     * @return string
     */
    public function getPurchaseKey();

    /**
     * Set purchaseKey
     *
     * @param string $purchaseKey
     * @return $this
     */
    public function setPurchaseKey($purchaseKey);
    
    /**
     * Get purchase order item
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface[]
     */
    public function getItems();

    /**
     * Set purchase order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface[] $item
     * @return $this
     */
    public function setItems($item);
}