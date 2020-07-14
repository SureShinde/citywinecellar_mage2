<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface ProductOriginalInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const SPECIAL_PRICE = 'special_price';
    const SPECIAL_FROM_DATE = 'special_from_date';
    const SPECIAL_TO_DATE = 'special_to_date';
    const BASE_COST = 'cost';
    const SHORT_DESCRIPTION = 'short_description';
    const DESCRIPTION = 'description';
    /*const MEDIA_GALLERY = 'media_gallery';*/
    const TAX_CLASS_ID = 'tax_class_id';
    const IS_IN_STOCK = 'is_in_stock';
    const MIN_SALE_QTY = 'min_sale_qty';
    const MAX_SALE_QTY = 'max_sale_qty';
    const QTY = 'qty';
    const ENABLE_QTY_INCREMENTS = 'enable_qty_increments';
    const QTY_INCREMENTS = 'qty_increments';
    const IS_QTY_DECIMAL = 'is_qty_decimal';
    const SHIPMENT_TYPE  = 'shipment_type';
    const PRICE_TYPE  = 'price_type';
    const WEIGHT_TYPE  = 'weight_type';

    /**#@-*/

    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Shipment Type
     *
     * @return int|null
     */
    public function getShipmentType();

    /**
     * Shipment Type
     *
     * @return int|null
     */
    public function getPriceType();

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get category ids by product
     *
     * @return string
     */
    public function getCategoryIds();

    /**
     * Product description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * @return int
     */
    /*public function getEnableQtyIncrements();*/

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Catalog\Api\Data\ProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Sets product image from it's child if possible
     *
     * @return string
     */
    public function getImage();

    /**
     * Retrieve images
     *
     * @return string[]
     */
    public function getImages();

    /**
     * @return int
     */
    /*public function getIsQtyDecimal();*/

    /**
     * @return int
     */
    public function getIsSalable();

    /**
     * get is virtual
     *
     * @return boolean
     */
    public function getIsVirtual();

    /**
     * Get minimum qty
     *
     * @return float|null
     */
   /* public function getMinimumQty();*/

    /**
     * Get maximum qty
     *
     * @return float|null
     */
    /*public function getMaximumQty();*/

    /**
     * Retrieve product has option
     *
     * @return int
     */
    public function hasOptions();


    /**
     * Product price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Get product qty increment
     *
     * @return float|null
     */
    /*public function getQtyIncrement();*/

    /**
     * get search string to search product
     *
     * @return string
     */
    public function getSearchString();

    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Product special price
     *
     * @return float|null
     */
    public function getSpecialPrice();

    /**
     * Product special price from date
     *
     * @return string|null
     */
    public function getSpecialFromDate();

    /**
     * Product special price to date
     *
     * @return string|null
     */
    public function getSpecialToDate();

    /**
     * Product status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Retrieve product tax class id
     *
     * @return int| string
     */
    public function getTaxClassId();

    /**
     * Product type id
     *
     * @return string|null
     */
    public function getTypeId();

    /**
     * Product updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Gets list of product tier prices
     *
     * @return \Magento\Catalog\Api\Data\ProductTierPriceInterface[]|null
     */
    public function getTierPrices();
    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes
     * @return ProductOriginalInterface
     */
//    public function setExtensionAttributes(\Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes);

    /**
     * Get base cost
     *
     * @return float|null
     */
    public function getBaseCost();

    /**
     * Get qty
     *
     * @return float|null
     */
    public function getQty();

    /**
     * Get fixed product tax
     *
     * @return string[]|null
     */
    public function getFpt();

    /**
     * @return int
     */
    /*public function getQtyIncrements();*/

    /**
     * Get product weight type
     *
     * @return int
     */
    public function getWeightType();

    /**
     * @return float
     */
    public function getWeight();

    /**
     * Get catalog rule product prices
     *
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface[]|null
     */
    public function getCatalogrulePrices();
}
