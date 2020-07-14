<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog\Product;

use Magento\Catalog\Model\ProductFactory;

/**
 * Class ConfigOptionsBuilder
 * @package Magestore\Webpos\Model\Catalog\Product
 */
class ConfigOptionsBuilder
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var int|null
     */
    protected $productId = null;

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_pricingHelper;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $_configurable;

    /**
     * ConfigOptionsBuilder constructor.
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     */
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->productFactory = $productFactory;
        $this->_objectManager = $objectManager;
        $this->_pricingHelper = $pricingHelper;
        $this->_configurable = $configurable;
    }

    /**
     * @param int $productId
     * @return void
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return int|null
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return ConfigOptionsInterface[]|null
     */
    public function create()
    {
        $configOptions = null;
        if ($this->getProductId()) {
            $product = $this->productFactory->create()->load($this->getProductId());
            $productAttributeOptions = $this->_configurable->getConfigurableAttributesAsArray($product);
            $options = $prices = array();
            $originalPrice = $product->getFinalPrice();
            $tempKey = 1;
            foreach ($productAttributeOptions as $productAttributeOption) {
                $values = $productAttributeOption['values'];
                $optionId = $productAttributeOption['attribute_id'];
                $code = $productAttributeOption['attribute_code'];
                $optionLabel = $productAttributeOption['label'];
                $options[$code]['optionId'] = $optionId;
                $options[$code]['optionLabel'] = $optionLabel;
                foreach ($values as $value) {
                    $optionValueId = $value['value_index'];
                    $pricing_value = (isset($value['pricing_value']) && $value['pricing_value'] != null) ? $value['pricing_value'] : 0;
                    $val = $value['label'];
                    $is_percent = (isset($value['is_percent']) && $value['is_percent'] != null) ? $value['is_percent'] : 0;
                    $options[$code][$optionValueId] = $val;
                    $childPrice = ($is_percent == 0) ? ($pricing_value) : ($pricing_value * $originalPrice / 100);
                    $prices[$code . $tempKey][$optionId] = $optionValueId;
                    $prices[$code . $tempKey]['isSaleable'] = 'true';
                    $prices[$code . $tempKey]['price'] = $this->formatPrice($childPrice);
                    $tempKey++;
                }
            }
            $options['price_condition'] = \Zend_Json::encode(array_values($prices));
            return $options;
        }
    }

    /**
     *
     * @param string $price
     * @return string
     */
    public function formatPrice($price){
        return $this->_pricingHelper->currency($price,true,false);
    }
}
