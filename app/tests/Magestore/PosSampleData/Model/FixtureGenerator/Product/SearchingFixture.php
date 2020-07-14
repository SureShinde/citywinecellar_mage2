<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magestore\Giftvoucher\Model\Product\Type\Giftvoucher;

/**
 * Class SearchingFixture
 *
 * @package Magestore\PosSampleData\Model\FixtureGenerator\Product
 */
class SearchingFixture
{
    protected $productTypes = [
        'fixture-' . Type::TYPE_SIMPLE,
        'fixture-' . Type::TYPE_VIRTUAL,
        BundleType::TYPE_CODE,
        Grouped::TYPE_CODE,
        Configurable::TYPE_CODE,
        'fixture-' . DownloadableType::TYPE_DOWNLOADABLE,
        Giftvoucher::GIFT_CARD_TYPE
    ];

    protected $searchingAttributes = [
        'sku',
        'name'
    ];

    protected $numberOfSearchingResult = [
        [
            'number' => 1,
            'searchString' => '1product_'
        ],
        [
            'number' => 4,
            'searchString' => '4products_'
        ],
        [
            'number' => 21,
            'searchString' => '21products_'
        ]
    ];

    /**
     * Get search fixtures
     *
     * @return array
     */
    public function getSearchFixtures()
    {
        $searchFixtures = [];

        foreach ($this->productTypes as $type) {
            foreach ($this->searchingAttributes as $attribute) {
                foreach ($this->numberOfSearchingResult as $value) {
                    $searchFixtures[$type][$attribute][] = [
                        'number' => $value['number'],
                        'searchString' => $attribute . $value['searchString']
                    ];
                }
            }
        }

        return $searchFixtures;
    }
}
