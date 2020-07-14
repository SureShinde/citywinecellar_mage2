<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;

/**
 * Class SimpleProductsFixture
 */
class SimpleProductsFixture extends AbstractTypeProductGenerator
{
    /**
     * Simple product sku pattern
     */
    const SKU_PATTERN = 'pos_simple_product_%s';

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD)
     */
    public function generateProduct($amount, $isEnable = true, $isVisibleOnPos = true, $hasCustomAttributes = false)
    {
        $fixtureMap = $this->getFixtureMap($isEnable, $isVisibleOnPos);
        $fixtureMap['type_id'] = Type::TYPE_SIMPLE;
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
