<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Option\Swatch;

/**
 * Class Swatch
 * @package Magestore\Webpos\Model\Catalog\Option\Swatch
 */
class Swatch extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Catalog\Option\Swatch\SwatchInterface
{
    /**
     * @inheritdoc
     */
    public function getSwatchId() {
        return $this->getData(self::SWATCH_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setSwatchId($swatchId) {
        return $this->setData(self::SWATCH_ID, $swatchId);
    }

    /**
     * @inheritdoc
     */
    public function getOptionId() {
        return $this->getData(self::OPTION_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setOptionId($optionId) {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * @inheritdoc
     */
    public function getType() {
        return $this->getData(self::TYPE);
    }	
    /**
     * @inheritdoc
     */
    public function setType($type) {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getValue() {
        return $this->getData(self::VALUE);
    }	
    /**
     * @inheritdoc
     */
    public function setValue($value) {
        return $this->setData(self::VALUE, $value);
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
        \Magestore\Webpos\Api\Data\Catalog\Option\Swatch\SwatchExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}