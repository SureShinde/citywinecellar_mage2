<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog\Option;

/**
 * Interface ConfigOptionsInterface
 */
interface ProductOptionsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CONFIG_OPTION = 'config_option';
    const CUSTOM_OPTION = 'custom_option';
    const BUNDLE_OPTION = 'bundle_option';
    const IS_OPTIONS = 'is_options';

    /**
     * Get Config Option
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterface[]
     */
    public function getConfigOption();

    /**
     * Set Config Option
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterface[] $configOption
     * @return ProductOptionsInterface
     */
    public function setConfigOption($configOption);


    /**
     * Get Custom Option
     *
     * @return string|null
     */
    public function getCustomOption();

    /**
     * Set Custom Option
     *
     * @param string $customOption
     * @return ProductOptionsInterface
     */
    public function setCustomOption($customOption);


    /**
     * Get Bundle Option
     *
     * @return string|null
     */
    public function getBundleOption();

    /**
     * Set Bundle Option
     *
     * @param string $bundleOption
     * @return ProductOptionsInterface
     */
    public function setBundleOption($bundleOption);


    /**
     * Get is Options
     *
     * @return int|null
     */
    public function getIsOptions();

    /**
     * Set is Options
     *
     * @param int $isOptions
     * @return ProductOptionsInterface
     */
    public function setIsOptions($isOptions);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsExtensionInterface $extensionAttributes
    );
}