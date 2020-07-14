<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductOptionsRepository implements \Magestore\Webpos\Api\Catalog\ProductOptionsRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface
     */
    protected $productOptions;
    /**
     * @var \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterfaceFactory
     */
    protected $configOptionsFactory;
    /**
     * @var \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionInterfaceFactory
     */
    protected $optionFactory;
    /**
     * @var \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductInterfaceFactory
     */
    protected $productInterfaceFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $currentProduct;
    protected $currentProductTypeInstance;

    /**
     * ProductOptionsRepository constructor.
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface $productOptions
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterfaceFactory $configOptionsFactory
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionInterfaceFactory $optionFactory
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductInterfaceFactory $productInterfaceFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface $productOptions,
        \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterfaceFactory $configOptionsFactory,
        \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionInterfaceFactory $optionFactory,
        \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductInterfaceFactory $productInterfaceFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->productOptions = $productOptions;
        $this->configOptionsFactory = $configOptionsFactory;
        $this->optionFactory = $optionFactory;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->productFactory = $productFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function getProductOptions($product_id) {
        $product = $this->productFactory->create()->load($product_id);

        $productOptions = $this->productOptions;

        if(!$product->getId()) {
            return $productOptions;
        }

        $this->currentProduct = $product;

        $this->addConfigOptions($product, $productOptions);

        return $productOptions;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface $productOptions
     */
    public function addConfigOptions($product, &$productOptions) {
        $configOptions = [];

        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeInstance */
            $productTypeInstance = $this->_getProductTypeInstance($product);
            $productAttributeOptions = $productTypeInstance->getConfigurableAttributesAsArray($product);
            $this->currentProductTypeInstance = $productTypeInstance;

            foreach ($productAttributeOptions as $productAttributeOption) {
                $productOptions->setIsOptions(1);
                /** @var \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterface $attribute */
                $attribute = $this->configOptionsFactory->create();
                $attribute->setId($productAttributeOption['attribute_id']);
                $attribute->setCode($productAttributeOption['attribute_code']);
                $attribute->setLabel($productAttributeOption['label']);
                $attribute->setPosition($productAttributeOption['position']);

                $this->addOptionToAttribute($attribute, $productAttributeOption);

                $configOptions[] = $attribute;
            }

        }

        $productOptions->setConfigOption($configOptions);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterface $attribute
     * @param array $productAttributeOption
     */
    public function addOptionToAttribute(&$attribute, $productAttributeOption) {
        $options = [];
        foreach ($productAttributeOption['values'] as $option) {
            /** @var \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionInterface $optionObj */
            $optionObj = $this->optionFactory->create();
            $optionObj->setId($option['value_index']);
            $optionObj->setLabel($option['label']);

            $this->addProductToOption($optionObj, $attribute);

            $options[] = $optionObj;
        }
        $attribute->setOptions($options);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\OptionInterface $optionObj
     * @param \Magestore\Webpos\Api\Data\Catalog\Option\ConfigOptionsInterface $attribute
     */
    public function addProductToOption(&$optionObj, $attribute) {
        $products = [];

        $usedProducts = $this->currentProductTypeInstance->getUsedProducts($this->currentProduct);
        foreach ($usedProducts as $product) {
            if($product->getData($attribute->getCode()) == $optionObj->getId()) {
                /** @var \Magestore\Webpos\Api\Data\Catalog\Option\Config\Attribute\Option\ProductInterface $pr */
                $pr = $this->productInterfaceFactory->create();
                $pr->setId($product->getId());
                $pr->setPrice(round($product->getPrice(), 4));
                $pr->setBasePrice(round($product->getBasePrice(), 4));

                $products[] = $pr;
            }
        }
        $optionObj->setProducts($products);
    }

    /**
     * get product type instance
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function _getProductTypeInstance($product = null)
    {
        $type = '';
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $type = 'Magento\ConfigurableProduct\Model\Product\Type\Configurable';
        }
        return $this->objectManager->get($type);
    }
}