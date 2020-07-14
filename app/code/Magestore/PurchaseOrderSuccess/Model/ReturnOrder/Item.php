<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ReturnOrder;

use \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;

/**
 * Class Item
 * @package Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item
 */
class Item extends \Magento\Framework\Model\AbstractModel
    implements ReturnOrderItemInterface
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item');
    }

    /**
     * Get return order item id
     *
     * @return int
     */
    public function getReturnItemId(){
        return $this->_getData(self::RETURN_ITEM_ID);
    }

    /**
     * Set return order item id
     *
     * @param int $returnItemId
     * @return $this
     */
    public function setReturnItemId($returnItemId){
        return $this->setData(self::RETURN_ITEM_ID, $returnItemId);
    }

    /**
     * Get return order id
     *
     * @return int
     */
    public function getReturnId(){
        return $this->_getData(self::RETURN_ID);
    }

    /**
     * Set return order id
     *
     * @param int $returnId
     * @return $this
     */
    public function setReturnId($returnId){
        return $this->setData(self::RETURN_ID, $returnId);
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId(){
        return $this->_getData(self::PRODUCT_ID);
    }

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId){
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get product sku
     *
     * @return string
     */
    public function getProductSku(){
        return $this->_getData(self::PRODUCT_SKU);
    }

    /**
     * Set product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku){
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName(){
        return $this->_getData(self::PRODUCT_NAME);
    }

    /**
     * Set product name
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName){
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * Get product supplier sku
     *
     * @return string
     */
    public function getProductSupplierSku(){
        return $this->_getData(self::PRODUCT_SUPPLIER_SKU);
    }

    /**
     * Set product supplier sku
     *
     * @param string $productSupplierSku
     * @return $this
     */
    public function setProductSupplierSku($productSupplierSku){
        return $this->setData(self::PRODUCT_SUPPLIER_SKU, $productSupplierSku);
    }

    /**
     * Get qty transferred
     *
     * @return float
     */
    public function getQtyTransferred(){
        return $this->_getData(self::QTY_TRANSFERRED);
    }

    /**
     * Set qty transferred
     *
     * @param float $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred){
        return $this->setData(self::QTY_TRANSFERRED, $qtyTransferred);
    }

    /**
     * Get qty returned
     *
     * @return float
     */
    public function getQtyReturned(){
        return $this->_getData(self::QTY_RETURNED);
    }

    /**
     * Set qty returned
     *
     * @param float $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned){
        return $this->setData(self::QTY_RETURNED, $qtyReturned);
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