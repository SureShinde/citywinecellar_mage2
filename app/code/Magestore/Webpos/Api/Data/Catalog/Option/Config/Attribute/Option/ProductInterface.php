<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option;

/**
 * Interface ConfigOptionsInterface
 */
interface ProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const PRICE = 'price';
    const BASE_PRICE = 'base_price';

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId();	
    /**
     * Set Id
     *
     * @param int $id
     * @return ProductInterface
     */
    public function setId($id);

    /**
     * Get Price
     *
     * @return float|null
     */
    public function getPrice();	
    /**
     * Set Price
     *
     * @param float $price
     * @return ProductInterface
     */
    public function setPrice($price);

    /**
     * Get Base Price
     *
     * @return float|null
     */
    public function getBasePrice();	
    /**
     * Set Base Price
     *
     * @param float $basePrice
     * @return ProductInterface
     */
    public function setBasePrice($basePrice);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductExtensionInterface $extensionAttributes
    );
}