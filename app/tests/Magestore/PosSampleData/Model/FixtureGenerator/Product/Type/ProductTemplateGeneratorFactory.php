<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magestore\Giftvoucher\Model\Product\Type\Giftvoucher;
use Magento\Framework\ObjectManagerInterface;

/**
 * Provide product template generator based on specified product type from fixture
 */
class ProductTemplateGeneratorFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $templateEntityMap = [
        Type::TYPE_SIMPLE => SimpleProductTemplateGenerator::class,
        Type::TYPE_VIRTUAL => VirtualProductTemplateGenerator::class,
        BundleType::TYPE_CODE => BundleProductTemplateGenerator::class,
        Grouped::TYPE_CODE => GroupedProductTemplateGenerator::class,
        Configurable::TYPE_CODE => ConfigurableProductTemplateGenerator::class,
        DownloadableType::TYPE_DOWNLOADABLE => DownloadableProductTemplateGenerator::class,
        Giftvoucher::GIFT_CARD_TYPE => GiftcardProductTemplateGenerator::class,
    ];

    protected $templateObjects = [];

    /**
     * ProductTemplateGeneratorFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param array $templateObjects
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $templateObjects = []
    ) {
        $this->objectManager = $objectManager;
        $this->templateObjects = $templateObjects;
    }

    /**
     * Create new object
     *
     * @param string $typeId
     * @return AbstractTypeProductGenerator
     * @throws \InvalidArgumentException
     */
    public function create($typeId)
    {
        if (!isset($this->templateEntityMap[$typeId])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot instantiate product template generator. Wrong type_id "%s" passed',
                    $typeId
                )
            );
        }

        $this->templateObjects[$typeId] = $this->objectManager->create($this->templateEntityMap[$typeId]);

        return $this->templateObjects[$typeId];
    }

    /**
     * Get template object
     *
     * @param string $typeId
     * @return AbstractTypeProductGenerator
     * @throws \InvalidArgumentException
     */
    public function get($typeId)
    {
        if (isset($this->templateObjects[$typeId])) {
            return $this->templateObjects[$typeId];
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Cannot instantiate product template generator. Wrong type_id "%s" passed',
                $typeId
            )
        );
    }
}
