<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product;

use Magestore\PosSampleData\Model\FixtureGenerator\Product\Type\ProductTemplateGeneratorFactory;

/**
 * Class ProductGenerator
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product
 */
class ProductGenerator
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
     * @param int $amount
     * @param bool $isEnable
     * @param bool $isVisibleOnPos
     * @param bool $hasCustomAttributes
     */
    public function execute($type, $amount, $isEnable = true, $isVisibleOnPos = true, $hasCustomAttributes = false)
    {
        $productGeneratorTemplate = $this->productTemplateGeneratorFactory->get($type);
        $productGeneratorTemplate->generateProduct($amount, $isEnable, $isVisibleOnPos, $hasCustomAttributes);
    }
}
