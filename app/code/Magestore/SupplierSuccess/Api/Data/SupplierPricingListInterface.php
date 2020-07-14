<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Api\Data;


interface SupplierPricingListInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SUPPLIER_ID = 'supplier_id';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_SKU = 'product_sku';
    const PRODUCT_NAME = 'product_name';
    const MINIMAL_QTY = 'minimal_qty';
    const COST = 'cost';
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';

    /**#@-*/

    /**
     * Supplier Pricelist id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set supplier pricelist id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Supplier id
     *
     * @return int|null
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
     * Product id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Set Product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Product sku
     *
     * @return string
     */
    public function getProductSku();

    /**
     * Set product sku
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku);

    /**
     * Product name
     *
     * @return string|null
     */
    public function getProductName();

    /**
     * Set product name
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName);

    /**
     * Minimal Qty
     *
     * @return float
     */
    public function getMinimalQty();

    /**
     * Set Minimal Qty
     *
     * @param float $minimalQty
     * @return $this
     */
    public function setMinimalQty($minimalQty);

    /**
     * Cost
     *
     * @return float
     */
    public function getCost();

    /**
     * Set Cost
     *
     * @param float $cost
     * @return $this
     */
    public function setCost($cost);

    /**
     * Start date
     *
     * @return string|null
     */
    public function getStartDate();

    /**
     * Set Start date
     *
     * @param string $startDate
     * @return $this
     */
    public function setStartDate($startDate);

    /**
     * End date
     *
     * @return string|null
     */
    public function getEndDate();

    /**
     * Set End date
     *
     * @param string $endDate
     * @return $this
     */
    public function setEndDate($endDate);

}