<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator;

use Magento\Catalog\Model\Product\Type;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\Framework\ObjectManagerInterface;
use Magestore\Giftvoucher\Model\Product\Type\Giftvoucher;
use Magestore\PosSampleData\Model\FixtureGenerator\Product\TemplateGenerator\VirtualProductTemplateGenerator;
use Magestore\PosSampleData\Model\FixtureGenerator\Product\TemplateGenerator\DownloadableProductTemplateGenerator;
use Magestore\PosSampleData\Model\FixtureGenerator\Product\TemplateGenerator\GiftcardProductTemplateGenerator;

/**
 * Provide product template generator based on specified product type from fixture
 *
 * Class ProductTemplateGeneratorFactory
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator
 */
class ProductTemplateGeneratorFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * ProductTemplateGeneratorFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Around function
     *
     * @param \Magento\Setup\Model\FixtureGenerator\ProductTemplateGeneratorFactory $subject
     * @param callable $proceed
     * @param array $fixture
     * @return mixed
     * @SuppressWarnings(PHPMD)
     */
    public function aroundCreate(
        \Magento\Setup\Model\FixtureGenerator\ProductTemplateGeneratorFactory $subject,
        callable $proceed,
        array $fixture
    ) {
        if (isset($fixture['type_id'])) {
            if ($fixture['type_id'] == Type::TYPE_VIRTUAL) {
                return $this->objectManager->create(VirtualProductTemplateGenerator::class, ['fixture' => $fixture]);
            } elseif ($fixture['type_id'] == DownloadableType::TYPE_DOWNLOADABLE) {
                return $this->objectManager->create(
                    DownloadableProductTemplateGenerator::class,
                    ['fixture' => $fixture]
                );
            }
        }

        return $proceed($fixture);
    }
}
