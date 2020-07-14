<?php

namespace Magestore\Webpos\Api\Data\Checkout\Order;

interface PaymentInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    const PAYMENT_ID = 'payment_id';
    const ORDER_ID = 'order_id';
    const SHIFT_ID = 'shift_id';
    const AMOUNT_PAID = 'amount_paid';
    const BASE_AMOUNT_PAID = 'base_amount_paid';
    const AMOUNT_CHANGE = 'amount_change';
    const BASE_AMOUNT_CHANGE = 'base_amount_change';
    const METHOD = 'method';
    const TITLE = 'title';
    const TRANSACTION_ID = 'transaction_id';
    const INVOICE_ID = 'invoice_id';
    const REFERENCE_NUMBER = 'reference_number';
    const CARD_TYPE = 'card_type';
    const PAYMENT_DATE = 'payment_date';
    const IS_PAID = 'is_paid';
    const IS_PAY_LATER = 'is_pay_later';
    const POS_PAYPAL_ACTIVE = 'pos_paypal_active';
    const POS_PAYPAL_INVOICE_ID = 'pos_paypal_invoice_id';
    const TYPE = 'type';
    const TYPE_CHECKOUT = 0;
    const TYPE_REFUND = 1;
    const SHIFT_INCREMENT_ID = 'shift_increment_id';
    const RECEIPT = 'receipt';
    const INCREMENT_ID = 'increment_id';
    const PARENT_INCREMENT_ID = 'parent_increment_id';
    const CC_LAST4 = 'cc_last4';

    /**
     * Get Payment Id
     *
     * @return int|null
     */
    public function getPaymentId();
    /**
     * Set Payment Id
     *
     * @param int|null $paymentId
     * @return PaymentInterface
     */
    public function setPaymentId($paymentId);

    /**
     * Get Order Id
     *
     * @return int|null
     */
    public function getOrderId();
    /**
     * Set Order Id
     *
     * @param int $orderId
     * @return PaymentInterface
     */
    public function setOrderId($orderId);

    /**
     * Get Shift Id
     *
     * @return int|null
     */
    public function getShiftId();
    /**
     * Set Shift Id
     *
     * @param int|null $shiftId
     * @return PaymentInterface
     */
    public function setShiftId($shiftId);

    /**
     * Get Amount Paid
     *
     * @return float|null
     */
    public function getAmountPaid();
    /**
     * Set Amount Paid
     *
     * @param float|null $amountPaid
     * @return PaymentInterface
     */
    public function setAmountPaid($amountPaid);

    /**
     * Get Base Amount Paid
     *
     * @return float|null
     */
    public function getBaseAmountPaid();
    /**
     * Set Base Amount Paid
     *
     * @param float|null $baseAmountPaid
     * @return PaymentInterface
     */
    public function setBaseAmountPaid($baseAmountPaid);

    /**
     * Get Amount Change
     *
     * @return float|null
     */
    public function getAmountChange();
    /**
     * Set Amount Change
     *
     * @param float|null $amountChange
     * @return PaymentInterface
     */
    public function setAmountChange($amountChange);

    /**
     * Get Base Amount Change
     *
     * @return float|null
     */
    public function getBaseAmountChange();
    /**
     * Set Base Amount Change
     *
     * @param float|null $baseAmountChange
     * @return PaymentInterface
     */
    public function setBaseAmountChange($baseAmountChange);

    /**
     * Get Method
     *
     * @return string|null
     */
    public function getMethod();
    /**
     * Set Method
     *
     * @param string|null $method
     * @return PaymentInterface
     */
    public function setMethod($method);

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle();
    /**
     * Set Title
     *
     * @param string|null $title
     * @return PaymentInterface
     */
    public function setTitle($title);

    /**
     * Get Transaction Id
     *
     * @return string|null
     */
    public function getTransactionId();
    /**
     * Set Transaction Id
     *
     * @param string|null $transactionId
     * @return PaymentInterface
     */
    public function setTransactionId($transactionId);

    /**
     * Get Invoice Id
     *
     * @return int|null
     */
    public function getInvoiceId();
    /**
     * Set Invoice Id
     *
     * @param int|null $invoiceId
     * @return PaymentInterface
     */
    public function setInvoiceId($invoiceId);

    /**
     * Get Reference Number
     *
     * @return string|null
     */
    public function getReferenceNumber();
    /**
     * Set Reference Number
     *
     * @param string|null $referenceNumber
     * @return PaymentInterface
     */
    public function setReferenceNumber($referenceNumber);

    /**
     * Get Payment Date
     *
     * @return string|null
     */
    public function getPaymentDate();
    /**
     * Set Payment Date
     *
     * @param string|null $paymentDate
     * @return PaymentInterface
     */
    public function setPaymentDate($paymentDate);

    /**
     * Get Is Paid
     *
     * @return int|null
     */
    public function getIsPaid();
    /**
     * Set Is Paid
     *
     * @param int|null $isPaid
     * @return PaymentInterface
     */
    public function setIsPaid($isPaid);

    /**
     * Get Card Type
     *
     * @return string|null
     */
    public function getCardType();
    /**
     * Set Card Type
     *
     * @param string|null $cardType
     * @return PaymentInterface
     */
    public function setCardType($cardType);

    /**
     * Get Is Pay Later
     *
     * @return int|null
     */
    public function getIsPayLater();
    /**
     * Set Is Pay Later
     *
     * @param int|null $isPayLater
     * @return PaymentInterface
     */
    public function setIsPayLater($isPayLater);

    /**
     * Get Pos Paypal Active
     *
     * @return int|null
     */
    public function getPosPaypalActive();
    /**
     * Set Pos Paypal Active
     *
     * @param int|null $posPaypalActive
     * @return PaymentInterface
     */
    public function setPosPaypalActive($posPaypalActive);

    /**
     * Get Pos Paypal Invoice Id
     *
     * @return string|null
     */
    public function getPosPaypalInvoiceId();
    /**
     * Set Pos Paypal Invoice Id
     *
     * @param string $posPaypalInvoiceId
     * @return PaymentInterface
     */
    public function setPosPaypalInvoiceId($posPaypalInvoiceId);

    /**
     * Get Type
     *
     * @return int|null
     */
    public function getType();
    /**
     * Set Type
     *
     * @param int|null $type
     * @return PaymentInterface
     */
    public function setType($type);
    /**
     * Set shift_increment_id
     *
     * @param string|null  $shift_increment_id
     * @return PaymentInterface
     */
    public function setShiftIncrementId($shift_increment_id);

    /**
     * Get shift_increment_id
     *
     * @return string|null
     */
    public function getShiftIncrementId();
    /**
     * Set Receipt
     *
     * @param string|null  $receipt
     * @return PaymentInterface
     */
    public function setReceipt($receipt);

    /**
     * Get Receipt
     *
     * @return string|null
     */
    public function getReceipt();
    /**
     * Set increment_id
     *
     * @param string|null  $increment_id
     * @return PaymentInterface
     */
    public function setIncrementId($increment_id);

    /**
     * Get increment_id
     *
     * @return string|null
     */
    public function getIncrementId();
    /**
     * Set parent_increment_id
     *
     * @param string|null  $parent_increment_id
     * @return PaymentInterface
     */
    public function setParentIncrementId($parent_increment_id);

    /**
     * Get parent_increment_id
     *
     * @return string|null
     */
    public function getParentIncrementId();

    /**
     * Get CcLast4
     *
     * @return string|null
     */
    public function getCcLast4();

    /**
     * Set CcLast4
     *
     * @param string|null $value
     * @return PaymentInterface
     */
    public function setCcLast4($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\Order\PaymentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\Order\PaymentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magestore\Webpos\Api\Data\Checkout\Order\PaymentExtensionInterface $extensionAttributes);
}
