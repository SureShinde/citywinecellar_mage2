<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute;

/**
 * Interface ConfigOptionsInterface
 */
interface OptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const LABEL = 'label';
    const PRODUCTS = 'products';

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
     * @return OptionInterface
     */
    public function setId($id);

    /**
     * Get Label
     *
     * @return string|null
     */
    public function getLabel();	
    /**
     * Set Label
     *
     * @param string $label
     * @return OptionInterface
     */
    public function setLabel($label);

    /**
     * Get Products
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductInterface[]
     */
    public function getProducts();	
    /**
     * Set Products
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductInterface[] $products
     * @return OptionInterface
     */
    public function setProducts($products);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionExtensionInterface $extensionAttributes
    );
}