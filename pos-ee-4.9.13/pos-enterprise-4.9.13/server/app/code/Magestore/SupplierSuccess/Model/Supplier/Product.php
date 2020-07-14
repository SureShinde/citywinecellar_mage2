<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\Supplier;

class Product extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product');
    }

    /**
     * Supplier Product id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData('supplier_product_id');
    }

    /**
     * Set supplier product id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('supplier_product_id', $id);
    }

    /**
     * Supplier id
     *
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set supplier id
     *
     * @param int $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId)
    {
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Product id
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->_getData(self::PRODUCT_ID);
    }

    /**
     * Set Product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Product sku
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->_getData(self::PRODUCT_SKU);
    }

    /**
     * Set product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku)
    {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * Product name
     *
     * @return string|null
     */
    public function getProductName()
    {
        return $this->_getData(self::PRODUCT_NAME);
    }

    /**
     * Set product name
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName)
    {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * Cost
     *
     * @return float
     */
    public function getCost()
    {
        return $this->_getData(self::COST);
    }

    /**
     * Set code
     *
     * @param float $cost
     * @return $this
     */
    public function setCost($cost)
    {
        return $this->setData(self::COST, $cost);
    }

    /**
     * Tax
     *
     * @return float
     */
    public function getTax()
    {
        return $this->_getData(self::TAX);
    }

    /**
     * Set tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax($tax)
    {
        return $this->setData(self::TAX, $tax);
    }

    /**
     * Product Supplier Sku
     *
     * @return string|null
     */
    public function getProductSupplierSku()
    {
        return $this->_getData(self::PRODUCT_SUPPLIER_SKU);
    }

    /**
     * Set Product Supplier Sku
     *
     * @param string $productSupplierSku
     * @return $this
     */
    public function setProductSupplierSku($productSupplierSku)
    {
        return $this->setData(self::PRODUCT_SUPPLIER_SKU, $productSupplierSku);
    }

}