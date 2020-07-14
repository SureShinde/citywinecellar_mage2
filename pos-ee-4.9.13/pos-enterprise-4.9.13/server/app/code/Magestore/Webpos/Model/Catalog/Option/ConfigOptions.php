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
class ConfigOptions extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterface
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
    public function getCode() {
        return $this->getData(self::CODE);
    }
    /**
     * @inheritdoc
     */
    public function setCode($code) {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getLabel() {
        return $this->getData(self::LABEL);
    }
    /**
     * @inheritdoc
     */
    public function setLabel($label) {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getOptions() {
        return $this->getData(self::OPTIONS);
    }
    /**
     * @inheritdoc
     */
    public function setOptions($options) {
        return $this->setData(self::OPTIONS, $options);
    }

    /**
     * @inheritdoc
     */
    public function getPosition() {
        return $this->getData(self::POSITION);
    }
    /**
     * @inheritdoc
     */
    public function setPosition($position) {
        return $this->setData(self::POSITION, $position);
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
        \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}