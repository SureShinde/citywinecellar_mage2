<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface SimpleProductInterface
{
    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Product type id
     *
     * @return string|null
     */
    public function getTypeId();

    /**
     * Retrieve product has option
     *
     * @return int
     */
    public function hasOptions();

    /**
     * Sets product image from it's child if possible
     *
     * @return string
     */
    public function getImage();

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Product price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Product status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Product updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Catalog\Api\Data\ProductExtensionInterface
     */
    public function getExtensionAttributes();
}
