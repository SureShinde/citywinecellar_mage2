<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

/**
 * Interface PackageItemInterface
 * @package Magestore\FulfilSuccess\Api\Data
 */
interface PackageItemInterface
{
    /**
     *
     */
    CONST PACKAGE_ITEM_ID = 'package_item_id';

    /**
     *
     */
    CONST PACKAGE_ID = 'package_id';

    /**
     *
     */
    CONST QTY = 'qty';

    /**
     *
     */
    CONST CUSTOMS_VALUE = 'customs_value';

    /**
     *
     */
    CONST PRICE = 'price';

    /**
     *
     */
    CONST NAME = 'name';

    /**
     *
     */
    CONST WEIGHT = 'weight';

    /**
     *
     */
    CONST PRODUCT_ID = 'product_id';

    /**
     *
     */
    CONST ORDER_ITEM_ID = 'order_item_id';

    /**
     * @return int
     */
    public function getPackageItemId();

    /**
     * @param $packageItemId
     * @return $this
     */
    public function setPackageItemId($packageItemId);

    /**
     * @return int
     */
    public function getPackageId();

    /**
     * @param $packageId
     * @return $this
     */
    public function setPackageId($packageId);

    /**
     * @return float
     */
    public function getQty();

    /**
     * @param $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * @return string
     */
    public function getCustomsValue();

    /**
     * @param $customsValue
     * @return $this
     */
    public function setCustomsValue($customsValue);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param $weight
     * @return $this
     */
    public function setWeight($weight);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @return int
     */
    public function getOrderItemId();

    /**
     * @param $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);
}