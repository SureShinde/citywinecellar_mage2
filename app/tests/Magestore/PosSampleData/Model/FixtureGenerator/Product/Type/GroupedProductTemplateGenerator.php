<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection as PosProductCollection;

/**
 * Class GroupedProductTemplateGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product\Type
 */
class GroupedProductTemplateGenerator extends AbstractTypeProductGenerator
{
    const SKU_PATTERN = "pos_grouped_product_%s";
    const CHILDREN_SKU_PATTERN = "pos_grouped_product_children_%s";

    /**
     * @inheritDoc
     */
    public function getProductTemplate($productNumber, $additionalData = [])
    {
        $productNumber = $this->formatProductNumber($productNumber);

        $numberOfChildren = 4;
        $this->generateGroupedChildrenProduct($numberOfChildren);
        $product = $this->productFactory->create(
            [
                'data' => [
                    'attribute_set_id' => $this->getDefaultAttributeSetId(),
                    'type_id' => Grouped::TYPE_CODE,
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
                    'stock_data' => [
                        'use_config_manage_stock' => 1,
                        'qty' => 1000,
                        'is_qty_decimal' => 0,
                        'is_in_stock' => 1
                    ],
                    'webpos_visible' => (isset($additionalData['webpos_visible']) && $additionalData['webpos_visible'])
                        ? PosProductCollection::VISIBLE_ON_WEBPOS
                        : 0
                ]
            ]
        );

        $position = 1;
        $associated = [];
        for ($i = 1; $i <= $numberOfChildren; $i++) {
            $linkedSku = sprintf(self::CHILDREN_SKU_PATTERN, $this->formatProductNumber($i));

            try {
                $linkedProduct = $this->productRepository->get($linkedSku);
                $linkedProductId = $linkedProduct->getId();
            } catch (\Exception $e) {
                $linkedProductId = '';
            }

            if ($linkedProductId) {
                $productLink = $this->productLinkInterfaceFactory->create();
                $productLink->setSku($product->getSku()) //sku of product group
                    ->setLinkType('associated')
                    ->setLinkedProductSku($linkedProduct->getSku())
                    ->setLinkedProductType($linkedProduct->getTypeId())
                    ->setPosition($position)
                    ->getExtensionAttributes()
                    ->setQty(1);
                $associated[] = $productLink;

                $position++;
            }
        }
        $product->setProductLinks($associated);

        return $product;
    }

    /**
     * Generate searching product
     *
     * @param int $numberOfResult
     * @param string $searchString
     * @param string $searchAttribute
     */
    public function generateSearchingProduct($numberOfResult, $searchString, $searchAttribute)
    {
        $additionalData = [
            'status' => 1,
            'webpos_visible' => 1
        ];

        $lastGeneratorId = $this->getLastGeneratorId(static::SKU_PATTERN);
        for ($i = 1; $i <= $numberOfResult; $i++) {
            $product = $this->getProductTemplate($lastGeneratorId + $i, $additionalData);
            try {
                $product->setData($searchAttribute, $searchString . $product->getData($searchAttribute));
                $product->setUrlKey($product->getSku());

                // Change parent sku before save
                if ($searchAttribute == 'sku') {
                    $associated = $product->getProductLinks();
                    $newAssociated = [];
                    foreach ($associated as $productLink) {
                        $productLink->setSku($product->getSku());
                        $newAssociated[] = $productLink;
                    }
                    $product->setProductLinks($associated);
                }

                $product->save();
            } catch (\Exception $e) {
                $this->logger->info(__('Could NOT generator product!'));
                $this->logger->info($e->getMessage());
                $this->logger->info($e->getTraceAsString());
            }
        }
    }

    /**
     * Generate grouped product children
     *
     * @param int $numberOfChildren
     * @return bool
     */
    public function generateGroupedChildrenProduct($numberOfChildren)
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
                $this->logger->info(__('Could NOT generator grouped children product!'));
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
