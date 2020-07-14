<?php

namespace Magestore\Webpos\Model\Checkout\Order;

use Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface;

class Payment extends \Magento\Framework\Model\AbstractModel implements PaymentInterface
{

    /**
     * @var string
     */
    protected $_eventPrefix = 'webpos_order_payment';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment::class);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentId() {
        return $this->getData(self::PAYMENT_ID);
    }
    /**
     * @inheritdoc
     */
    public function setPaymentId($paymentId) {
        return $this->setData(self::PAYMENT_ID, $paymentId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId() {
        return $this->getData(self::ORDER_ID);
    }
    /**
     * @inheritdoc
     */
    public function setOrderId($orderId) {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getShiftId() {
        return $this->getData(self::SHIFT_ID);
    }
    /**
     * @inheritdoc
     */
    public function setShiftId($shiftId) {
        return $this->setData(self::SHIFT_ID, $shiftId);
    }

    /**
     * @inheritdoc
     */
    public function getAmountPaid() {
        return $this->getData(self::AMOUNT_PAID);
    }
    /**
     * @inheritdoc
     */
    public function setAmountPaid($amountPaid) {
        return $this->setData(self::AMOUNT_PAID, round($amountPaid, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseAmountPaid() {
        return $this->getData(self::BASE_AMOUNT_PAID);
    }
    /**
     * @inheritdoc
     */
    public function setBaseAmountPaid($baseAmountPaid) {
        return $this->setData(self::BASE_AMOUNT_PAID, round($baseAmountPaid, 4));
    }

    /**
     * @inheritdoc
     */
    public function getAmountChange()
    {
        return $this->getData(self::AMOUNT_CHANGE);
    }

    /**
     * @inheritdoc
     */
    public function setAmountChange($amountChange)
    {
        return $this->setData(self::AMOUNT_CHANGE, round($amountChange, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBaseAmountChange()
    {
        return $this->getData(self::BASE_AMOUNT_CHANGE);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmountChange($baseAmountChange)
    {
        return $this->setData(self::BASE_AMOUNT_CHANGE, round($baseAmountChange, 4));
    }

    /**
     * @inheritdoc
     */
    public function getMethod() {
        return $this->getData(self::METHOD);
    }
    /**
     * @inheritdoc
     */
    public function setMethod($method) {
        return $this->setData(self::METHOD, $method);
    }

    /**
     * @inheritdoc
     */
    public function getTitle() {
        return $this->getData(self::TITLE);
    }
    /**
     * @inheritdoc
     */
    public function setTitle($title) {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getTransactionId() {
        return $this->getData(self::TRANSACTION_ID);
    }
    /**
     * @inheritdoc
     */
    public function setTransactionId($transactionId) {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceId() {
        return $this->getData(self::INVOICE_ID);
    }
    /**
     * @inheritdoc
     */
    public function setInvoiceId($invoiceId) {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    /**
     * @inheritdoc
     */
    public function getReferenceNumber() {
        return $this->getData(self::REFERENCE_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setReferenceNumber($referenceNumber) {
        return $this->setData(self::REFERENCE_NUMBER, $referenceNumber);
    }

    /**
     * @inheritdoc
     */
    public function getIsPaid()
    {
        return $this->getData(self::IS_PAID);
    }
    /**
     * @inheritdoc
     */
    public function setIsPaid($isPaid)
    {
        return $this->setData(self::IS_PAID, $isPaid);
    }
    /**
     * @inheritdoc
     */
    public function getCardType() {
        return $this->getData(self::CARD_TYPE);
    }
    /**
     * @inheritdoc
     */
    public function setCardType($cardType) {
        return $this->setData(self::CARD_TYPE, $cardType);
    }


    /**
     * @inheritdoc
     */
    public function getPaymentDate()
    {
        return $this->getData(self::PAYMENT_DATE);
    }
    /**
     * @inheritdoc
     */
    public function setPaymentDate($paymentDate)
    {
        return $this->setData(self::PAYMENT_DATE, $paymentDate);
    }

    /**
     * @inheritdoc
     */
    public function getPosPaypalActive()
    {
        return $this->getData(self::POS_PAYPAL_ACTIVE);
    }
    /**
     * @inheritdoc
     */
    public function setPosPaypalActive($posPaypalActive)
    {
        return $this->setData(self::POS_PAYPAL_ACTIVE, $posPaypalActive);
    }

    /**
     * @inheritdoc
     */
    public function getPosPaypalInvoiceId()
    {
        return $this->getData(self::POS_PAYPAL_INVOICE_ID);
    }
    /**
     * @inheritdoc
     */
    public function setPosPaypalInvoiceId($posPaypalInvoiceId)
    {
        return $this->setData(self::POS_PAYPAL_INVOICE_ID, $posPaypalInvoiceId);
    }

    /**
     * @inheritdoc
     */
    public function getIsPayLater() {
        return $this->getData(self::IS_PAY_LATER);
    }
    /**
     * @inheritdoc
     */
    public function setIsPayLater($isPayLater) {
        return $this->setData(self::IS_PAY_LATER, $isPayLater);
    }


    /**
     * @inheritdoc
     */
    public function getType() {
        return $this->getData(self::TYPE);
    }
    /**
     * @inheritdoc
     */
    public function setType($type) {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @param null|string $shift_increment_id
     * @return PaymentInterface
     */
    public function setShiftIncrementId($shift_increment_id)
    {
        return $this->setData(self::SHIFT_INCREMENT_ID, $shift_increment_id);
    }

    /**
     * @return null|string
     */
    public function getShiftIncrementId()
    {
        return $this->getData(self::SHIFT_INCREMENT_ID);
    }

    /**
     * Set Receipt
     *
     * @param string|null  $receipt
     * @return PaymentInterface
     */
    public function setReceipt($receipt) {
        return $this->setData(self::RECEIPT, $receipt);
    }

    /**
     * Get Receipt
     *
     * @return string|null
     */
    public function getReceipt() {
        return $this->getData(self::RECEIPT);
    }

    /**
     * @param null|string $increment_id
     * @return PaymentInterface
     */
    public function setIncrementId($increment_id)
    {
        return $this->setData(self::INCREMENT_ID, $increment_id);
    }

    /**
     * @return null|string
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @param string $parent_increment_id
     * @return PaymentInterface
     */
    public function setParentIncrementId($parent_increment_id)
    {
        return $this->setData(self::PARENT_INCREMENT_ID, $parent_increment_id);
    }

    /**
     * @return null|string
     */
    public function getParentIncrementId()
    {
        return $this->getData(self::PARENT_INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCcLast4()
    {
        return $this->getData(self::CC_LAST4);
    }

    /**
     * @inheritDoc
     */
    public function setCcLast4($value)
    {
        return $this->setData(self::CC_LAST4, $value);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes() {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(\Magestore\Webpos\Api\Data\Checkout\Order\PaymentExtensionInterface $extensionAttributes){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
