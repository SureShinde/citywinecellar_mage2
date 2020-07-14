<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Model\Product\Visibility;
use Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection as PosProductCollection;

/**
 * Class BundleProductTemplateGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product\Type
 */
class BundleProductTemplateGenerator extends AbstractTypeProductGenerator
{
    const SKU_PATTERN = "pos_bundle_product_%s";
    const CHILDREN_SKU_PATTERN = "pos_bundle_product_children_%s";

    /**
     * @inheritDoc
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getProductTemplate($productNumber, $additionalData = [])
    {
        $productNumber = $this->formatProductNumber($productNumber);

        $bundleOptions = 2;
        $bundleProductsPerOption = 3;
        $this->generateBundleChildrenProduct($bundleOptions * $bundleProductsPerOption);
        $bundleVariationSkuPattern = self::CHILDREN_SKU_PATTERN;
        $bundleProduct = $this->productFactory->create(
            [
                'data' => [
                    'attribute_set_id' => $this->getDefaultAttributeSetId(),
                    'type_id' => BundleType::TYPE_CODE,
                    'name' => sprintf(static::SKU_PATTERN, $productNumber),
                    'url_key' => sprintf('template-url' . static::SKU_PATTERN, $productNumber),
                    'sku' => sprintf(static::SKU_PATTERN, $productNumber),
                    'price' => 10,
                    'visibility' => Visibility::VISIBILITY_BOTH,
                    'status' => (isset($additionalData['status']) && $additionalData['status'])
                        ? Status::STATUS_ENABLED
                        : Status::STATUS_DISABLED,
                    'website_ids' => [1],
                    'category_ids' => [2],
                    'weight' => 1,
                    'description' => 'description',
                    'short_description' => 'short description',
                    'tax_class_id' => 2, //'taxable goods',
                    'price_type' => \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED,
                    'price_view' => 1,
                    'stock_data' => [
                        'use_config_manage_stock' => 1,
                        'qty' => 1000,
                        'is_qty_decimal' => 0,
                        'is_in_stock' => 1
                    ],
                    'can_save_bundle_selections' => true,
                    'affect_bundle_product_selections' => true,
                    'webpos_visible' => (isset($additionalData['webpos_visible']) && $additionalData['webpos_visible'])
                        ? PosProductCollection::VISIBLE_ON_WEBPOS
                        : 0
                ]
            ]
        );

        $bundleProductOptions = [];
        $variationN = 0;
        for ($i = 1; $i <= $bundleOptions; $i++) {
            $option = $this->bundleOptionFactory->create(
                [
                    'data' => [
                        'title' => 'Bundle Product Items ' . $productNumber . ' ' . $i,
                        'default_title' => 'Bundle Product Items ' . $productNumber . ' ' . $i,
                        'type' => 'select',
                        'required' => 1,
                        'delete' => '',
                        'position' => $bundleOptions - $i,
                        'option_id' => '',
                    ]
                ]
            );
            $option->setSku($bundleProduct->getSku());

            $links = [];
            for ($linkN = 1; $linkN <= $bundleProductsPerOption; $linkN++) {
                $variationN++;
                $variationNumber = $this->formatProductNumber($variationN);
                $link = $this->linkFactory->create(
                    [
                        'data' => [
                            'sku' => sprintf($bundleVariationSkuPattern, $variationNumber),
                            'qty' => 1,
                            'can_change_qty' => 1,
                            'position' => $linkN - 1,
                            'price_type' => 0,
                            'price' => 0.0,
                            'option_id' => '',
                            'is_default' => $linkN === 1,
                        ]
                    ]
                );
                $links[] = $link;
            }
            $option->setProductLinks($links);
            $bundleProductOptions[] = $option;
        }

        $extension = $bundleProduct->getExtensionAttributes();
        $extension->setBundleProductOptions($bundleProductOptions);
        $bundleProduct->setExtensionAttributes($extension);
        // Need for set "has_options" field
        $bundleProduct->setBundleOptionsData($bundleProductOptions);
        $bundleSelections = array_map(
            function ($option) {
                return array_map(
                    function ($link) {
                        return $link->getData();
                    },
                    $option->getProductLinks()
                );
            },
            $bundleProductOptions
        );
        $bundleProduct->setBundleSelectionsData($bundleSelections);

        return $bundleProduct;
    }

    /**
     * Generate bundle product children
     *
     * @param int $numberOfChildren
     * @return bool
     */
    public function generateBundleChildrenProduct($numberOfChildren)
    {
        $additionalData = [
            'status' => true,
            'webpos_visible' => false
        ];

        $totalCurrentChildren = $this->getLastGeneratorId(self::CHILDREN_SKU_PATTERN);

        if ($totalCurrentChildren >= $numberOfChildren) {
            return true;
        }

        for ($i = 1; $i <= $numberOfChildren - $totalCurrentChildren; $i++) {
            $productNumber = $this->formatProductNumber($totalCurrentChildren + $i);
            $product = $this->getChildrenTemplate($totalCurrentChildren + $i, $additionalData);
            $sku = sprintf(self::CHILDREN_SKU_PATTERN, $productNumber);
            $url = sprintf('template-url' . self::CHILDREN_SKU_PATTERN, $productNumber);
            $product->setSku($sku);
            $product->setName($sku);
            $product->setUrlKey($url);
            try {
                $product->save();
            } catch (\Exception $e) {
                $this->logger->info(__('Could NOT generator bundle children product!'));
                $this->logger->info($e->getMessage());
                $this->logger->info($e->getTraceAsString());
            }
        }
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
}
