<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice;

use \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface;

/**
 * Class Item
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice
 */
class Item extends \Magento\Framework\Model\AbstractModel
    implements InvoiceItemInterface
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item');
    }

    /**
     * Get purchase order invoice item id
     *
     * @return int
     */
    public function getPurchaseOrderInvoiceItemId(){
        return $this->_getData(self::PURCHASE_ORDER_INVOICE_ITEM_ID);
    }

    /**
     * Set purchase order invoice item id
     *
     * @param int $purchaseOrderInvoiceItemId
     * @return $this
     */
    public function setPurchaseOrderInvoiceItemId($purchaseOrderInvoiceItemId){
        return $this->setData(self::PURCHASE_ORDER_INVOICE_ITEM_ID, $purchaseOrderInvoiceItemId);
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
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_ID);
    }

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_ID, $purchaseOrderItemId);
    }

    /**
     * Get qty billed
     *
     * @return float
     */
    public function getQtyBilled(){
        return $this->_getData(self::QTY_BILLED);
    }

    /**
     * Set qty billed
     *
     * @param float $qtyBilled
     * @return $this
     */
    public function setQtyBilled($qtyBilled){
        return $this->setData(self::QTY_BILLED, $qtyBilled);
    }

    /**
     * Get unit price
     *
     * @return float
     */
    public function getUnitPrice(){
        return $this->_getData(self::UNIT_PRICE);
    }

    /**
     * Set unit price
     *
     * @param float $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice){
        return $this->setData(self::UNIT_PRICE, $unitPrice);
    }

    /**
     * Get tax
     *
     * @return float
     */
    public function getTax(){
        return $this->_getData(self::TAX);
    }

    /**
     * Set tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax($tax){
        return $this->setData(self::TAX, $tax);
    }

    /**
     * Get discount
     *
     * @return float
     */
    public function getDiscount(){
        return $this->_getData(self::DISCOUNT);
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount){
        return $this->setData(self::DISCOUNT, $discount);
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