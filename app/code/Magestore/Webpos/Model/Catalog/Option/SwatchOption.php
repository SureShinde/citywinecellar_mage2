<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Option;

/**
 * Class BundleOptionsBuilder
 * @package Magestore\Webpos\Model\Catalog\Product
 */
class SwatchOption extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Catalog\Option\SwatchOptionInterface
{
    /**
     * @inheritdoc
     */
    public function getAttributeId() {
        return $this->getData(self::ATTRIBUTE_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setAttributeId($attributeId) {
        return $this->setData(self::ATTRIBUTE_ID, $attributeId);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeCode() {
        return $this->getData(self::ATTRIBUTE_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setAttributeCode($attributeCode) {
        return $this->setData(self::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeLabel() {
        return $this->getData(self::ATTRIBUTE_LABEL);
    }	
    /**
     * @inheritdoc
     */
    public function setAttributeLabel($attributeLabel) {
        return $this->setData(self::ATTRIBUTE_LABEL, $attributeLabel);
    }

    /**
     * @inheritdoc
     */
    public function getSwatches() {
        return $this->getData(self::SWATCHES);
    }	
    /**
     * @inheritdoc
     */
    public function setSwatches($swatches) {
        return $this->setData(self::SWATCHES, $swatches);
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
        \Magestore\Webpos\Api\Data\Catalog\Option\SwatchOptionExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}