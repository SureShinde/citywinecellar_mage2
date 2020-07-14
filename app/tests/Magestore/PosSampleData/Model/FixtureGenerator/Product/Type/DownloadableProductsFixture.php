<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Model\ProductFactory;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;

/**
 * Class DownloadableProductsFixture
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product\Type
 */
class DownloadableProductsFixture extends AbstractTypeProductGenerator
{
    /**
     * Simple product sku pattern
     */
    const SKU_PATTERN = 'pos_downloadable_product_%s';
    const PRODUCT_TYPE_ID = DownloadableType::TYPE_DOWNLOADABLE;

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD)
     */
    public function generateProduct($amount, $isEnable = true, $isVisibleOnPos = true, $hasCustomAttributes = false)
    {
        $fixtureMap = $this->getFixtureMap($isEnable, $isVisibleOnPos);
        $this->productGeneratorFactory->create()->generate($amount, $fixtureMap);
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
        $this->generateSearchingFixtureProduct($numberOfResult, $searchString, $searchAttribute);
    }
}
