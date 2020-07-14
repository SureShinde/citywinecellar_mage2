<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;

/**
 * Class VirtualProductTemplateGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product\Type
 */
class VirtualProductTemplateGenerator extends AbstractTypeProductGenerator
{
    const SKU_PATTERN = "pos_virtual_product_%s";

    /**
     * Get product generator template
     *
     * @param int $productNumber
     * @param array $additionalData
     * @return ProductInterface
     */
    public function getProductTemplate($productNumber, $additionalData = [])
    {
        $product = parent::getProductTemplate($productNumber, $additionalData);
        $product->setTypeId(Type::TYPE_VIRTUAL);
        $product->setWeight('');

        return $product;
    }
}
