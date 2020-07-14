<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\Supplier;

class PricingList extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList');
    }

    /**
     * Supplier Pricelist id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData('supplier_pricinglist_id');
    }

    /**
     * Set supplier pricelist id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('supplier_pricinglist_id', $id);
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
     * Minimal Qty
     *
     * @return float
     */
    public function getMinimalQty()
    {
        return $this->_getData(self::MINIMAL_QTY);
    }

    /**
     * Set Minimal Qty
     *
     * @param float $minimalQty
     * @return $this
     */
    public function setMinimalQty($minimalQty)
    {
        return $this->setData(self::MINIMAL_QTY, $minimalQty);
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
     * Set Cost
     *
     * @param float $cost
     * @return $this
     */
    public function setCost($cost)
    {
        return $this->setData(self::COST, $cost);
    }

    /**
     * Start date
     *
     * @return string|null
     */
    public function getStartDate()
    {
        return $this->_getData(self::START_DATE);
    }

    /**
     * Set Start date
     *
     * @param string $startDate
     * @return $this
     */
    public function setStartDate($startDate)
    {
        return $this->setData(self::START_DATE, $startDate);
    }

    /**
     * End date
     *
     * @return string|null
     */
    public function getEndDate()
    {
        return $this->_getData(self::END_DATE);
    }

    /**
     * Set End date
     *
     * @param string $endDate
     * @return $this
     */
    public function setEndDate($endDate)
    {
        return $this->setData(self::END_DATE, $endDate);
    }

}