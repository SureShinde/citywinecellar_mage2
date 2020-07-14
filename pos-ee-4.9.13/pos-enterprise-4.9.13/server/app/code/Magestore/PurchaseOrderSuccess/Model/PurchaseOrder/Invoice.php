<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder;

use \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface;

/**
 * Class Invoice
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder
 */
class Invoice extends \Magento\Framework\Model\AbstractModel
    implements InvoiceInterface
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice');
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
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId(){
        return $this->_getData(self::PURCHASE_ORDER_ID);
    }

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId){
        return $this->setData(self::PURCHASE_ORDER_ID, $purchaseOrderId);
    }

    /**
     * Get invoice code
     *
     * @return string
     */
    public function getInvoiceCode(){
        if(!$this->_getData(self::INVOICE_CODE)){
            $formatId = pow(10, self::CODE_LENGTH + 1) + $this->getId();
            $formatId = (string) $formatId;
            $formatId = substr($formatId, 0-self::CODE_LENGTH);
            $this->setInvoiceCode($formatId);
        }
        return $this->_getData(self::INVOICE_CODE);
    }

    /**
     * Set invoice code
     *
     * @param string $invoiceCode
     * @return $this
     */
    public function setInvoiceCode($invoiceCode){
        return $this->setData(self::INVOICE_CODE, $invoiceCode);
    }

    /**
     * Get billed at
     *
     * @return string
     */
    public function getBilledAt(){
        return $this->_getData(self::BILLED_AT);
    }

    /**
     * Set billed at
     *
     * @param string $billedAt
     * @return $this
     */
    public function setBilledAt($billedAt){
        return $this->setData(self::BILLED_AT, $billedAt);
    }

    /**
     * Get total qty billed
     *
     * @return float
     */
    public function getTotalQtyBilled(){
        return $this->_getData(self::TOTAL_QTY_BILLED);
    }

    /**
     * Set total qty billed
     *
     * @param float $totalQtyBilled
     * @return $this
     */
    public function setTotalQtyBilled($totalQtyBilled){
        return $this->setData(self::TOTAL_QTY_BILLED, $totalQtyBilled);
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal(){
        return $this->_getData(self::SUBTOTAL);
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal){
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * Get total tax
     *
     * @return float
     */
    public function getTotalTax(){
        return $this->_getData(self::TOTAL_TAX);
    }

    /**
     * Set total tax
     *
     * @param float $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax){
        return $this->setData(self::TOTAL_TAX, $totalTax);
    }

    /**
     * Get total discount
     *
     * @return float
     */
    public function getTotalDiscount(){
        return $this->_getData(self::TOTAL_DISCOUNT);
    }

    /**
     * Set total discount
     *
     * @param float $totalDiscount
     * @return $this
     */
    public function setTotalDiscount($totalDiscount){
        return $this->setData(self::TOTAL_DISCOUNT, $totalDiscount);
    }

    /**
     * Get grand total exclude tax
     *
     * @return float
     */
    public function getGrandTotalExclTax(){
        return $this->_getData(self::GRAND_TOTAL_EXCL_TAX);
    }

    /**
     * Set grand total exclude tax
     *
     * @param float $grandTotalExclTax
     * @return $this
     */
    public function setGrandTotalExclTax($grandTotalExclTax){
        return $this->setData(self::GRAND_TOTAL_EXCL_TAX, $grandTotalExclTax);
    }

    /**
     * Get grand total include tax
     *
     * @return float
     */
    public function getGrandTotalInclTax(){
        return $this->_getData(self::GRAND_TOTAL_INCL_TAX);
    }

    /**
     * Set grand total include tax
     *
     * @param float $grandTotalInclTax
     * @return $this
     */
    public function setGrandTotalInclTax($grandTotalInclTax){
        return $this->setData(self::GRAND_TOTAL_INCL_TAX, $grandTotalInclTax);
    }

    /**
     * Get total due
     *
     * @return float
     */
    public function getTotalDue(){
        return $this->_getData(self::TOTAL_DUE);
    }

    /**
     * Set total due
     *
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue){
        return $this->setData(self::TOTAL_DUE, $totalDue);
    }

    /**
     * Get total refund
     *
     * @return float
     */
    public function getTotalRefund(){
        return $this->_getData(self::TOTAL_REFUND);
    }

    /**
     * Set total refund
     *
     * @param float $totalRefund
     * @return $this
     */
    public function setTotalRefund($totalRefund){
        return $this->setData(self::TOTAL_REFUND, $totalRefund);
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

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt(){
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt){
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}