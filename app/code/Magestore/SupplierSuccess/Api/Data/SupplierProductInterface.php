<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Api\Data;


interface SupplierProductInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SUPPLIER_ID = 'supplier_id';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_SKU = 'product_sku';
    const PRODUCT_NAME = 'product_name';
    const COST = 'cost';
    const TAX = 'tax';
    const PRODUCT_SUPPLIER_SKU = 'product_supplier_sku';
    const SUPPLIER_PRODUCT_ADD_NEW = 'supplier_product_add_new';

    /**#@-*/

    /**
     * Supplier Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set supplier product id
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
     * Cost
     *
     * @return float
     */
    public function getCost();

    /**
     * Set code
     *
     * @param float $cost
     * @return $this
     */
    public function setCost($cost);

    /**
     * Tax
     *
     * @return float
     */
    public function getTax();

    /**
     * Set tax
     *
     * @param float $tax
     * @return $this
     */
    public function setTax($tax);

    /**
     * Product Supplier Sku
     *
     * @return string|null
     */
    public function getProductSupplierSku();

    /**
     * Set Product Supplier Sku
     *
     * @param string $productSupplierSku
     * @return $this
     */
    public function setProductSupplierSku($productSupplierSku);

}