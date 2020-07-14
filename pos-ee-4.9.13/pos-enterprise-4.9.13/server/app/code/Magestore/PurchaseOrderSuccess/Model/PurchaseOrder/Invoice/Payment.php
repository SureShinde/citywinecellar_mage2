<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice;

use \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface;

/**
 * Class Payment
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice
 */
class Payment extends \Magento\Framework\Model\AbstractModel
    implements PaymentInterface
{
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Payment');
    }

    /**
     * Get purchase order invoice payment id
     *
     * @return int
     */
    public function getPurchaseOrderInvoicePaymentId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_PAYMENT_ID);
    }

    /**
     * Set purchase order invoice payment id
     *
     * @param int $purchaseOrderInvoicePaymentId
     * @return $this
     */
    public function setPurchaseOrderInvoicePaymentId($purchaseOrderInvoicePaymentId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_PAYMENT_ID, $purchaseOrderInvoicePaymentId);
    }

    /**
     * Get purchase order invoice id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_ID);
    }

    /**
     * Set purchase order invoice id
     *
     * @param int $purchaseOrderInvoiceId
     * @return $this
     */
    public function setPurchaseOrderInvoiceId($purchaseOrderInvoiceId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_ID, $purchaseOrderInvoiceId);
    }

    /**
     * Get payment method
     *
     * @return string
     */
    public function getPaymentMethod(){
        return $this->_getData(self::PAYMENT_METHOD);
    }

    /**
     * Set payment method
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod){
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * Get payment amount
     *
     * @return float
     */
    public function getPaymentAmount(){
        return $this->_getData(self::PAYMENT_AMOUNT);
    }

    /**
     * Set payment amount
     *
     * @param float $paymentAmount
     * @return $this
     */
    public function setPaymentAmount($paymentAmount){
        return $this->setData(self::PAYMENT_AMOUNT, $paymentAmount);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(){
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description){
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get payment at
     *
     * @return string
     */
    public function getPaymentAt(){
        return $this->_getData(self::PAYMENT_AT);
    }

    /**
     * Set payment at
     *
     * @param string $paymentAt
     * @return $this
     */
    public function setPaymentAt($paymentAt){
        return $this->setData(self::PAYMENT_AT, $paymentAt);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}