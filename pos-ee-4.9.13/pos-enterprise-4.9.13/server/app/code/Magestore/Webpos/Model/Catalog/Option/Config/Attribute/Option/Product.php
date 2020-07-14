<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Option\Config\Attribute\Option;

/**
 * Class BundleOptionsBuilder
 * @package Magestore\Webpos\Model\Catalog\Product
 */
class Product extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductInterface
{

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getData(self::ID);
    }	
    /**
     * @inheritdoc
     */
    public function setId($id) {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getPrice() {
        return $this->getData(self::PRICE);
    }	
    /**
     * @inheritdoc
     */
    public function setPrice($price) {
        return $this->setData(self::PRICE, round($price, 4));
    }

    /**
     * @inheritdoc
     */
    public function getBasePrice() {
        return $this->getData(self::BASE_PRICE);
    }	
    /**
     * @inheritdoc
     */
    public function setBasePrice($basePrice) {
        return $this->setData(self::BASE_PRICE, round($basePrice, 4));
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}