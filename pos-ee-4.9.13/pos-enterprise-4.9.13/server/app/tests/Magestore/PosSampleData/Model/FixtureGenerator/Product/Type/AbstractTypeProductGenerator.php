<?php

namespace Magestore\PosSampleData\Model\FixtureGenerator\Product\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection as PosProductCollection;
use Magento\Bundle\Api\Data\LinkInterfaceFactory;
use Magento\Bundle\Api\Data\OptionInterfaceFactory as BundleOptionInterfaceFactory;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory as ConfigurableOptionFactory;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Setup\Model\FixtureGenerator\ProductGeneratorFactory;

/**
 * Class AbstractType
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractTypeProductGenerator
{
    const CODE_LENGTH = 8;
    const SKU_PATTERN = '';
    const PRODUCT_TYPE_ID = '';

    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var BundleOptionInterfaceFactory\
     */
    protected $bundleOptionFactory;

    /**
     * @var LinkInterfaceFactory
     */
    protected $linkFactory;
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Repository
     */
    protected $productAttributeRepository;
    /**
     * @var ConfigurableOptionFactory
     */
    protected $configurableOptionFactory;
    /**
     * @var ProductLinkInterfaceFactory
     */
    protected $productLinkInterfaceFactory;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductGeneratorFactory
     */
    protected $productGeneratorFactory;

    /**
     * AbstractTypeProductGenerator constructor.
     *
     * @param ProductFactory $productFactory
     * @param CollectionFactory $collectionFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param BundleOptionInterfaceFactory $bundleOptionFactory
     * @param LinkInterfaceFactory $linkFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository
     * @param ConfigurableOptionFactory $configurableOptionFactory
     * @param ProductLinkInterfaceFactory $productLinkInterfaceFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param ProductGeneratorFactory $productGeneratorFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ProductFactory $productFactory,
        CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        BundleOptionInterfaceFactory $bundleOptionFactory,
        LinkInterfaceFactory $linkFactory,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        ConfigurableOptionFactory $configurableOptionFactory,
        ProductLinkInterfaceFactory $productLinkInterfaceFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        ProductGeneratorFactory $productGeneratorFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $collectionFactory;
        $this->logger = $logger;
        $this->optionFactory = $optionFactory;
        $this->bundleOptionFactory = $bundleOptionFactory;
        $this->linkFactory = $linkFactory;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->configurableOptionFactory = $configurableOptionFactory;
        $this->productLinkInterfaceFactory = $productLinkInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->productGeneratorFactory = $productGeneratorFactory;
    }

    /**
     * Generate product
     *
     * @param int $amount
     * @param bool $isEnable
     * @param bool $isVisibleOnPos
     * @param bool $hasCustomAttributes
     */
    public function generateProduct($amount, $isEnable = true, $isVisibleOnPos = true, $hasCustomAttributes = false)
    {
        $additionalData = [
            'status' => $isEnable,
            'webpos_visible' => $isVisibleOnPos
        ];

        $lastGeneratorId = $this->getLastGeneratorId(static::SKU_PATTERN);

        for ($i = 1; $i <= $amount; $i++) {
            $product = $this->getProductTemplate($lastGeneratorId + $i, $additionalData);
            try {
                if ($hasCustomAttributes) {
                    $dataOption = [
                        "sort_order" => 1,
                        "title" => "Pos Customer Option",
                        "price_type" => "fixed",
                        "price" => "",
                        "type"  => "field",
                        "max_characters" => "225",
                        "is_require" => 0
                    ];
                    /** @var \Magento\Catalog\Model\Product\Option $option */
                    $option = $this->optionFactory->create();
                    $option->setProductSku($product->getSku())
                        ->addData($dataOption);
                    $product->setOptions([$option]);
                    $product->setHasOptions(1);
                    $product->setCanSaveCustomOptions(true);
                }
                $product->save();
            } catch (\Exception $e) {
                $this->logger->info(__('Could NOT generator product!'));
                $this->logger->info($e->getMessage());
                $this->logger->info($e->getTraceAsString());
            }
        }
    }

    /**
     * Get last generator id
     *
     * @param string $productSkuPattern
     * @return int
     */
    public function getLastGeneratorId($productSkuPattern)
    {
        $productSkuPattern = str_replace('%s', '[0-9]+', $productSkuPattern);
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->getSelect()
            ->where('sku ?', new \Zend_Db_Expr('REGEXP \'^' . $productSkuPattern . '$\''));

        return max(0, $productCollection->getSize());
    }

    /**
     * Format product number to 8-digits format (00000001)
     *
     * @param int $productNumber
     * @return string
     */
    public function formatProductNumber($productNumber)
    {
        return str_pad($productNumber, self::CODE_LENGTH, "0", STR_PAD_LEFT);
    }

    /**
     * Get product generator template
     *
     * @param int $productNumber
     * @param array $additionalData
     * @return ProductInterface
     */
    public function getProductTemplate($productNumber, $additionalData = [])
    {
        $productNumber = $this->formatProductNumber($productNumber);

        // template for simple product
        $product = $this->productFactory->create(
            [
                'data' => [
                    'attribute_set_id' => $this->getDefaultAttributeSetId(),
                    'type_id' => Type::TYPE_SIMPLE,
                    'name' => sprintf(static::SKU_PATTERN, $productNumber),
                    'url_key' => sprintf('template-url' . static::SKU_PATTERN, $productNumber),
                    'sku' => sprintf(static::SKU_PATTERN, $productNumber),
                    'price' => 10,
                    'visibility' => Visibility::VISIBILITY_BOTH,
                    'status' => (isset($additionalData['status']) && $additionalData['status'])
                        ? Status::STATUS_ENABLED
                        : Status::STATUS_DISABLED,
                    'website_ids' => [1, 0],
                    'category_ids' => [2],
                    'weight' => 1,
                    'description' => 'description',
                    'short_description' => 'short description',
                    'tax_class_id' => 2, //'taxable goods',
                    'stock_data' => [
                        'use_config_manage_stock' => 1,
                        'qty' => 1000,
                        'is_qty_decimal' => 0,
                        'is_in_stock' => 1
                    ],
                    'webpos_visible' => (isset($additionalData['webpos_visible']) && $additionalData['webpos_visible'])
                        ? PosProductCollection::VISIBLE_ON_WEBPOS
                        : 0
                ]
            ]
        );

        $additionalAttributes = isset($additionalData['additional_attributes']) ?: [];

        foreach ($additionalAttributes as $attributeCode => $attributeValue) {
            $product->setData($attributeCode, $attributeValue);
        }

        return $product;
    }

    /**
     * Get fixture map data
     *
     * @param bool $isEnable
     * @param bool $isVisibleOnPos
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function getFixtureMap($isEnable = true, $isVisibleOnPos = true)
    {
        return [
            'name' => function ($productId) {
                return sprintf(static::SKU_PATTERN, $this->formatProductNumber($productId));
            },
            'sku' => function ($productId) {
                return sprintf(static::SKU_PATTERN, $this->formatProductNumber($productId));
            },
            'price' => 10,
            'type_id' => static::PRODUCT_TYPE_ID ?: TYPE::TYPE_SIMPLE,
            'url_key' => function ($productId) {
                return sprintf(static::SKU_PATTERN, $this->formatProductNumber($productId));
            },
            'description' => 'description',
            'short_description' => 'short description',
            'website_ids' => function ($index, $entityNumber) {
                return [0, 1];
            },
            'category_ids' => function ($index, $entityNumber) {
                return 2;
            },
            'attribute_set_id' => $this->getDefaultAttributeSetId(),
            'status' => ($isEnable == true || $isEnable == 1) ? Status::STATUS_ENABLED : Status::STATUS_DISABLED,
            'webpos_visible' => $isVisibleOnPos ? PosProductCollection::VISIBLE_ON_WEBPOS : 0
        ];
    }

    /**
     * Get default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->productFactory->create()->getDefaultAttributeSetId();
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
        $additionalData = [
            'status' => 1,
            'webpos_visible' => 1
        ];

        $lastGeneratorId = $this->getLastGeneratorId(static::SKU_PATTERN);
        for ($i = 1; $i <= $numberOfResult; $i++) {
            $product = $this->getProductTemplate($lastGeneratorId + $i, $additionalData);
            try {
                $product->setData($searchAttribute, $searchString . $product->getData($searchAttribute));
                $product->setUrlKey($product->getSku());
                $product->save();
            } catch (\Exception $e) {
                $this->logger->info(__('Could NOT generator product!'));
                $this->logger->info($e->getMessage());
                $this->logger->info($e->getTraceAsString());
            }
        }
    }

    /**
     * Generate searching product
     *
     * @param int $numberOfResult
     * @param string $searchString
     * @param string $searchAttribute
     */
    public function generateSearchingFixtureProduct($numberOfResult, $searchString, $searchAttribute)
    {
        $fixtureMap = $this->getFixtureMap();

        switch ($searchAttribute) {
            case 'name':
                $fixtureMap[$searchAttribute] = function ($productId) use ($fixtureMap, $searchString) {
                    return $searchString . $fixtureMap['name']($productId);
                };
                break;
            case 'sku':
                $fixtureMap[$searchAttribute] = function ($productId) use ($fixtureMap, $searchString) {
                    return $searchString . $fixtureMap['sku']($productId);
                };
                break;
            default:
                $fixtureMap[$searchAttribute] = $searchString . ($fixtureMap[$searchAttribute] ?: '');
                break;
        }

        $this->productGeneratorFactory->create()->generate($numberOfResult, $fixtureMap);
    }
}
