<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magestore\Giftvoucher\Model\Product\Type\Giftvoucher;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;
use Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection as PosProductCollection;

/**
 * Class ProductGenerateData
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductGenerateData
{
    const ENABLE_VISIBLE = 1;
    const ENABLE_INVISIBLE = 2;
    const UNABLE_VISIBLE = 3;
    const UNABLE_INVISIBLE = 4;
    const ENABLE_VISIBLE_CUSTOM_OPTION = 5;

    protected $productQuantity = [
        Type::TYPE_SIMPLE => [
            self::ENABLE_VISIBLE_CUSTOM_OPTION => 25
        ],
        'fixture-' . Type::TYPE_SIMPLE => [
            self::ENABLE_VISIBLE => 94,
            self::ENABLE_INVISIBLE => 75,
            self::UNABLE_VISIBLE => 75,
            self::UNABLE_INVISIBLE => 50
        ],
        'fixture-' . Type::TYPE_VIRTUAL => [
            self::ENABLE_VISIBLE => 24,
            self::ENABLE_INVISIBLE => 25,
            self::UNABLE_VISIBLE => 25,
            self::UNABLE_INVISIBLE => 10
        ],
        BundleType::TYPE_CODE => [
            self::ENABLE_VISIBLE => 14,
            self::ENABLE_INVISIBLE => 5,
            self::UNABLE_VISIBLE => 5,
            self::UNABLE_INVISIBLE => 5
        ],
        Grouped::TYPE_CODE => [
            self::ENABLE_VISIBLE => 14,
            self::ENABLE_INVISIBLE => 5,
            self::UNABLE_VISIBLE => 5,
            self::UNABLE_INVISIBLE => 5
        ],
        Configurable::TYPE_CODE => [
            self::ENABLE_VISIBLE => 24,
            self::ENABLE_INVISIBLE => 10,
            self::UNABLE_VISIBLE => 10,
            self::UNABLE_INVISIBLE => 10
        ],
        'fixture-' . DownloadableType::TYPE_DOWNLOADABLE => [
            self::ENABLE_VISIBLE => 24,
            self::ENABLE_INVISIBLE => 25,
            self::UNABLE_VISIBLE => 25,
            self::UNABLE_INVISIBLE => 10
        ],
        Giftvoucher::GIFT_CARD_TYPE => [
            self::ENABLE_VISIBLE => 24,
            self::ENABLE_INVISIBLE => 25,
            self::UNABLE_VISIBLE => 25,
            self::UNABLE_INVISIBLE => 10
        ],
    ];

    protected $typeCreated = [
        //"Product is ENABLE && VISIBLE on POS"
        self::ENABLE_VISIBLE => [
            'is_enable' => 1,
            'is_visible_on_pos' => 1,
            'has_custom_attribute' => 0
        ],
        //"Product is ENABLE && INVISIBLE on POS"
        self::ENABLE_INVISIBLE => [
            'is_enable' => 1,
            'is_visible_on_pos' => 0,
            'has_custom_attribute' => 0
        ],
        //"Product is UNABLE && VISIBLE on POS"
        self::UNABLE_VISIBLE => [
            'is_enable' => 0,
            'is_visible_on_pos' => 1,
            'has_custom_attribute' => 0
        ],
        //"Product is UNABLE && INVISIBLE on POS"
        self::UNABLE_INVISIBLE => [
            'is_enable' => 0,
            'is_visible_on_pos' => 0,
            'has_custom_attribute' => 0
        ],
        //"Product is ENABLE && VISIBLE on POS and also has custom option"
        self::ENABLE_VISIBLE_CUSTOM_OPTION => [
            'is_enable' => 1,
            'is_visible_on_pos' => 1,
            'has_custom_attribute' => 1
        ],
    ];

    /**
     * @var ProductGenerator
     */
    protected $productGenerator;
    /**
     * @var ProductSearchingDataGenerator
     */
    protected $productSearchingDataGenerator;
    /**
     * @var SearchingFixture
     */
    protected $searchingFixture;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var GetSourceItemBySourceCodeAndSku
     */
    protected $getSourceItemBySourceCodeAndSku;
    /**
     * @var SourceItemsSaveInterface
     */
    protected $sourceItemsSave;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * ProductGenerateData constructor.
     *
     * @param ProductGenerator $productGenerator
     * @param ProductSearchingDataGenerator $productSearchingDataGenerator
     * @param SearchingFixture $searchingFixture
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ProductGenerator $productGenerator,
        ProductSearchingDataGenerator $productSearchingDataGenerator,
        SearchingFixture $searchingFixture,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku,
        SourceItemsSaveInterface $sourceItemsSave,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->productGenerator = $productGenerator;
        $this->productSearchingDataGenerator = $productSearchingDataGenerator;
        $this->searchingFixture = $searchingFixture;
        $this->indexerFactory = $indexerFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->getSourceItemBySourceCodeAndSku = $getSourceItemBySourceCodeAndSku;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->logger = $logger;
    }

    /**
     * Generate products
     */
    public function execute()
    {
        $idsIndexer = [
            'catalog_category_product',
            'catalog_product_category',
            'catalog_product_attribute',
            'cataloginventory_stock',
            'inventory',
            'catalogrule_product',
            'catalog_product_price',
            'catalogsearch_fulltext',
            'webpos_search_product'
        ];

        $indexersState = [];
        foreach ($idsIndexer as $indexerId) {
            /** @var \Magento\Indexer\Model\Indexer $indexer */
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId);
            $indexersState[$indexerId] = $indexer->isScheduled();
            $indexer->setScheduled(true);
        }

        $this->generateSampleProduct();
        $this->generateSearchingProduct();
        $this->generateOutOfStockProduct();

        foreach ($idsIndexer as $indexerId) {
            /** @var \Magento\Indexer\Model\Indexer $indexer */
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->setScheduled($indexersState[$indexerId]);
            $indexer->reindexAll();
        }
    }

    /**
     * Generate Sample Product
     */
    public function generateSampleProduct()
    {
        foreach ($this->productQuantity as $productType => $productsQty) {
            foreach ($productsQty as $type => $qty) {
                $this->productGenerator->execute(
                    $productType,
                    $qty,
                    $this->typeCreated[$type]['is_enable'],
                    $this->typeCreated[$type]['is_visible_on_pos'],
                    $this->typeCreated[$type]['has_custom_attribute']
                );
            }
        }
    }

    /**
     * Generate Searching Product
     */
    public function generateSearchingProduct()
    {
        $searchingFixtures = $this->searchingFixture->getSearchFixtures();

        foreach ($searchingFixtures as $type => $attributeFixtures) {
            foreach ($attributeFixtures as $attribute => $fixtures) {
                foreach ($fixtures as $fixture) {
                    $this->productSearchingDataGenerator->execute(
                        $type,
                        $fixture['number'],
                        $fixture['searchString'],
                        $attribute
                    );
                }
            }
        }
    }

    /**
     * Generate Out Of Stock Product
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function generateOutOfStockProduct()
    {
        $sourceItems = [];

        // Product with discount
        $discountProductSku = '24-UG06';
        $discountProductSourceItem = $this->getSourceItem($discountProductSku);
        if ($discountProductSourceItem && $discountProductSourceItem->getSku()) {
            $discountProductSourceItem->setStatus(1);
            $discountProductSourceItem->setQuantity(1000);
            $sourceItems[] = $discountProductSourceItem;
        }

        // Bundle children
        $skuBundleChildren = '24-WG083-blue';
        $bundleSourceItem = $this->getSourceItem($skuBundleChildren);
        if ($bundleSourceItem && $bundleSourceItem->getSku()) {
            $bundleSourceItem->setStatus(0);
            $sourceItems[] = $bundleSourceItem;
        }

        // Group children
        $skuGroupedChildren = '24-WG087';
        $groupedSourceItem = $this->getSourceItem($skuGroupedChildren);
        if ($groupedSourceItem && $groupedSourceItem->getSku()) {
            $groupedSourceItem->setStatus(0);
            $sourceItems[] = $groupedSourceItem;
        }

        // Simple
        $simpleProduct = $this->getLastProductWithType(Type::TYPE_SIMPLE, 'pos_simple_product');
        $simpleSku = 'pos_simple_product_out_of_stock';
        if ($simpleProduct && $simpleProduct->getId()) {
            $simpleProduct->setSku($simpleSku);
            $simpleProduct->setWebposVisible(PosProductCollection::VISIBLE_ON_WEBPOS);
            try {
                $simpleProduct->save();
                $simpleSourceItem = $this->getSourceItem($simpleSku);
                if ($simpleSourceItem && $simpleSourceItem->getSku()) {
                    $simpleSourceItem->setStatus(0);
                    $sourceItems[] = $simpleSourceItem;
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getTraceAsString());
            }
        }

        // Virtual
        $virtualProduct = $this->getLastProductWithType(Type::TYPE_VIRTUAL);
        $virtualSku = 'pos_virtual_product_out_of_stock';
        if ($virtualProduct && $virtualProduct->getId()) {
            $virtualProduct->setSku($virtualSku);
            $virtualProduct->setWebposVisible(PosProductCollection::VISIBLE_ON_WEBPOS);
            try {
                $virtualProduct->save();
                $virtualSourceItem = $this->getSourceItem($virtualSku);
                if ($virtualSourceItem && $virtualSourceItem->getSku()) {
                    $virtualSourceItem->setStatus(0);
                    $sourceItems[] = $virtualSourceItem;
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getTraceAsString());
            }
        }

        // Configurable
        $configProduct = $this->getLastProductWithType(Configurable::TYPE_CODE);
        $configurableSku = 'pos_configurable_product_out_of_stock';
        if ($configProduct && $configProduct->getId()) {
            $configProduct->setSku($configurableSku);
            $configProduct->setWebposVisible(PosProductCollection::VISIBLE_ON_WEBPOS);
            try {
                $configProduct->save();
                $children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
                /** @var \Magento\Catalog\Model\Product $child */
                foreach ($children as $child) {
                    $configurableChildSourceItem = $this->getSourceItem($child->getSku());
                    if ($configurableChildSourceItem
                        && $configurableChildSourceItem->getSku()) {
                        $configurableChildSourceItem->setStatus(0);
                        $sourceItems[] = $configurableChildSourceItem;
                        break;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getTraceAsString());
            }
        }

        // Save source item
        $this->sourceItemsSave->execute($sourceItems);
    }

    /**
     * Get product
     *
     * @param string $type
     * @param string $sku
     * @return \Magento\Catalog\Model\Product
     */
    public function getLastProductWithType($type, $sku = null)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('type_id', $type);
        if ($sku) {
            $collection->addFieldToFilter('sku', ['like' => "%$sku%"]);
        }
        $collection->setOrder('entity_id', 'DESC');
        return $collection->getFirstItem();
    }

    /**
     * Get source item
     *
     * @param string $sku
     * @return SourceItemInterface|bool
     */
    public function getSourceItem($sku)
    {
        $defaultSourceCode = 'default';
        try {
            return $this->getSourceItemBySourceCodeAndSku->execute($defaultSourceCode, $sku);
        } catch (\Exception $e) {
            return false;
        }
    }
}
