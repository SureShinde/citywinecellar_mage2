<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product;

use Magestore\PosSampleData\Model\FixtureGenerator\Product\Type\ProductTemplateGeneratorFactory;

/**
 * Class ProductSearchingDataGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product
 */
class ProductSearchingDataGenerator
{
    /**
     * @var ProductTemplateGeneratorFactory
     */
    protected $productTemplateGeneratorFactory;

    /**
     * ProductGenerator constructor.
     *
     * @param ProductTemplateGeneratorFactory $productTemplateGeneratorFactory
     */
    public function __construct(
        ProductTemplateGeneratorFactory $productTemplateGeneratorFactory
    ) {
        $this->productTemplateGeneratorFactory = $productTemplateGeneratorFactory;
    }

    /**
     * Generate product
     *
     * @param string $type
     * @param int $numberOfResult
     * @param string $searchString
     * @param string $searchAttribute
     */
    public function execute($type, $numberOfResult, $searchString, $searchAttribute)
    {
        $productGeneratorTemplate = $this->productTemplateGeneratorFactory->get($type);
        $productGeneratorTemplate->generateSearchingProduct($numberOfResult, $searchString, $searchAttribute);
    }
}
