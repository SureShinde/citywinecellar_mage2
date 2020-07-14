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
class ProductOptions extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigOption() {
        return $this->getData(self::CONFIG_OPTION);
    }	
    /**
     * @inheritdoc
     */
    public function setConfigOption($configOption) {
        return $this->setData(self::CONFIG_OPTION, $configOption);
    }

    /**
     * @inheritdoc
     */
    public function getCustomOption() {
        return $this->getData(self::CUSTOM_OPTION);
    }	
    /**
     * @inheritdoc
     */
    public function setCustomOption($customOption) {
        return $this->setData(self::CUSTOM_OPTION, $customOption);
    }

    /**
     * @inheritdoc
     */
    public function getBundleOption() {
        return $this->getData(self::BUNDLE_OPTION);
    }	
    /**
     * @inheritdoc
     */
    public function setBundleOption($bundleOption) {
        return $this->setData(self::BUNDLE_OPTION, $bundleOption);
    }

    /**
     * @inheritdoc
     */
    public function getIsOptions() {
        return $this->getData(self::IS_OPTIONS);
    }
    /**
     * @inheritdoc
     */
    public function setIsOptions($isOptions) {
        return $this->setData(self::IS_OPTIONS, $isOptions);
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
        \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}