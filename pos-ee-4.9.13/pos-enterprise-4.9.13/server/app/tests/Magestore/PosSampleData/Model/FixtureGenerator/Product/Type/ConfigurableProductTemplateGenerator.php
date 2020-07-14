<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection as PosProductCollection;

/**
 * Class ConfigurableProductTemplateGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product\Type
 */
class ConfigurableProductTemplateGenerator extends AbstractTypeProductGenerator
{
    const SKU_PATTERN = "pos_configurable_product_%s";
    const CHILDREN_SKU_PATTERN = "pos_child_configurable_product_%s";
    const CONFIGURABLE_ATTRIBUTE = "pos_configurable";

    protected $configurableOptions = [];
    
    /**
     * @inheritDoc
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getProductTemplate($productNumber, $additionalData = [])
    {
        $productNumber = $this->formatProductNumber($productNumber);
        $productAttributes = $this->getConfigurableOptions();

        $product = $this->productFactory->create(
            [
                'data' => [
                    'attribute_set_id' => $this->getDefaultAttributeSetId(),
                    'type_id' => Configurable::TYPE_CODE,
                    'name' => sprintf(static::SKU_PATTERN, $productNumber),
                    'url_key' => sprintf('template-url' . static::SKU_PATTERN, $productNumber),
                    'sku' => sprintf(static::SKU_PATTERN, $productNumber),
                    'meta_description' => 'Configurable Product',
                    'meta_keyword' => sprintf(static::SKU_PATTERN, $productNumber),
                    'meta_title' => sprintf(static::SKU_PATTERN, $productNumber),
                    'price' => 10,
                    'visibility' => Visibility::VISIBILITY_BOTH,
                    'status' => (isset($additionalData['status']) && $additionalData['status'])
                        ? Status::STATUS_ENABLED
                        : Status::STATUS_DISABLED,
                    'website_ids' => [1, 0],
                    'category_ids' => [2],
                    'weight' => 1,
                    'description' => 'description',
                    'short_description' => 'short description',
                    'tax_class_id' => 2, //'taxable goods',
                    'stock_data' => [
                        'use_config_manage_stock' => 1,
                        'qty' => 1000,
                        'is_qty_decimal' => 0,
                        'is_in_stock' => 1
                    ],
                    // Need for set "has_options" field
                    'can_save_configurable_attributes' => true,
                    'configurable_attributes_data' => $productAttributes,
                    'webpos_visible' => (isset($additionalData['webpos_visible']) && $additionalData['webpos_visible'])
                        ? PosProductCollection::VISIBLE_ON_WEBPOS
                        : 0
                ]
            ]
        );

        $attributes = [];
        $childrenAdditionalData = [
            'status' => true,
            'webpos_visible' => false
        ];
        $associateChildrenIds = [];
        foreach ($productAttributes as $index => $attribute) {
            $attributeValues = [];
            foreach ($attribute['values'] as $value) {
                $attributeValues[] = [
                    'label' => $value['label'],
                    'attribute_id' => $attribute['id'],
                    'value_index' => $value['value']
                ];
                // create child product
                $sku = sprintf(self::CHILDREN_SKU_PATTERN . '_' . $value['value'], $productNumber);
                try {
                    $existedProduct = $this->productRepository->get($sku);
                    $existedProductId = $existedProduct->getId();
                } catch (\Exception $e) {
                    $existedProductId = '';
                }
                if ($existedProductId) {
                    $associateChildrenIds[] = $existedProduct->getId();
                } else {
                    $url = sprintf('template-url' . self::CHILDREN_SKU_PATTERN . $value['value'], $productNumber);
                    $childProduct = $this->getChildrenTemplate($index, $childrenAdditionalData);
                    $childProduct->setSku($sku);
                    $childProduct->setName($sku);
                    $childProduct->setUrlKey($url);
                    $childProduct->setData(self::CONFIGURABLE_ATTRIBUTE, $value['value']);
                    try {
                        $childProduct->save();
                        $associateChildrenIds[] = $childProduct->getId();
                    } catch (\Exception $e) {
                        $this->logger->info(__('Could NOT generator configurable children product!'));
                        $this->logger->info($e->getMessage());
                        $this->logger->info($e->getTraceAsString());
                    }
                }
            }
            $attributes[] = [
                'attribute_id' => $attribute['id'],
                'code' => $attribute['name'],
                'label' => $attribute['name'],
                'position' => $index,
                'values' => $attributeValues,
            ];
        }
        $configurableOptions = $this->configurableOptionFactory->create($attributes);
        $extensionConfigurableAttributes = $product->getExtensionAttributes();
        $extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
        $extensionConfigurableAttributes->setConfigurableProductLinks($associateChildrenIds);
        $product->setExtensionAttributes($extensionConfigurableAttributes);

        return $product;
    }

    /**
     * Get children template data
     *
     * @param int $productNumber
     * @param array $additionalData
     * @return ProductInterface
     */
    protected function getChildrenTemplate($productNumber, $additionalData = [])
    {
        return parent::getProductTemplate($productNumber, $additionalData);
    }

    /**
     * Get configurable option
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigurableOptions()
    {
        if (!count($this->configurableOptions)) {
            $attribute = $this->productAttributeRepository->get(self::CONFIGURABLE_ATTRIBUTE);
            $options = $attribute->getOptions();

            $this->configurableOptions[0] = [
                'id' => $attribute->getAttributeId(),
                'name' => $attribute->getDefaultFrontendLabel(),
                'values' => []
            ];
            foreach ($options as $option) {
                if ($option->getValue()) {
                    $this->configurableOptions[0]['values'][] = [
                        'label' => $option->getLabel(),
                        'value' => $option->getValue()
                    ];
                }
            }
        }

        return $this->configurableOptions;
    }
}
