<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Downloadable\Model\Product\Type as DownloadableType;

/**
 * Class DownloadableProductTemplateGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product\Type
 */
class DownloadableProductTemplateGenerator extends AbstractTypeProductGenerator
{
    const SKU_PATTERN = "pos_downloadable_product_%s";

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
        $product->setTypeId(DownloadableType::TYPE_DOWNLOADABLE);
        $product->setWeight('');

        return $product;
    }
}
