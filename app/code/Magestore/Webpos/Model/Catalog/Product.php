<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Catalog;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\EntryConverterPool;
use Magento\Framework\Api\AttributeValueFactory;
use Magestore\Webpos\Helper\Profiler;
use Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface;
use Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface;
use Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\CollectionFactory as PriceCollectionFactory;
use Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterfaceFactory;
use Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterfaceFactory;

/**
 * Catalog product model
 *
 * @method Product setHasError(bool $value)
 * @method \Magento\Catalog\Model\ResourceModel\Product getResource()
 * @method null|bool getHasError()
 * @method Product setAssociatedProductIds(array $productIds)
 * @method array getAssociatedProductIds()
 * @method Product setNewVariationsAttributeSetId(int $value)
 * @method int getNewVariationsAttributeSetId()
 * @method \Magento\Catalog\Model\ResourceModel\Product\Collection getCollection()
 * @method string getUrlKey()
 * @method Product setUrlKey(string $urlKey)
 * @method Product setRequestPath(string $requestPath)
 * @method Product setWebsiteIds(array $ids)
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends \Magento\Catalog\Model\Product implements \Magestore\Webpos\Api\Data\Catalog\ProductInterface
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $_configurableProduct;

    /**
     * @var \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
     */
    protected $_configurableProductBlock;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_helperImage;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $_emulation;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_formatInterface;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrencyInterface;

    /**
     * @var \Magestore\Webpos\Api\Inventory\StockItemRepositoryInterface
     */
    protected $_stockItemRepositoryInterFace;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $_webposHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    protected $_groupedProductType;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistryInterface;

    /**
     * @var \Magento\Bundle\Model\Option
     */
    protected $_bundleOption;

    /**
     * @var \Magento\Bundle\Model\Product\Type
     */

    protected $_bundleProductType;
    /**
     * @var \Magestore\Webpos\Api\Catalog\ProductOptionsRepositoryInterface
     */
    protected $productOptionsRepository;

    /**
     * @var
     */
    protected $_productOptions;

    /**
     * @var \Magento\Weee\Model\Tax
     */
    protected $weeTax;

    /**
     * @var \Magestore\Webpos\Api\Catalog\ProductSearchRepositoryInterface
     */
    protected $productSearchRepository;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\CollectionFactory
     */
    protected $catalogRuleProductPricecollectionFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterfaceFactory
     */
    protected $giftCardPriceOptionsInterfaceFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardTemplateInterfaceFactory
     */
    protected $giftCardTemplateInterfaceFactory;

    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Product constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataService
     * @param \Magento\Catalog\Model\Product\Url $url
     * @param \Magento\Catalog\Model\Product\Link $productLink
     * @param \Magento\Catalog\Model\Product\Configuration\Item\OptionFactory $itemOptionFactory
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory
     * @param \Magento\Catalog\Model\Product\OptionFactory $catalogProductOptionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $catalogProductStatus
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Catalog\Model\ResourceModel\Product $resource
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $resourceCollection
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param \Magento\Catalog\Model\Indexer\Product\Eav\Processor $productEavIndexerProcessor
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\Product\Image\CacheFactory $imageCacheFactory
     * @param \Magento\Catalog\Model\ProductLink\CollectionProvider $entityCollectionProvider
     * @param \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider
     * @param \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkFactory
     * @param \Magento\Catalog\Api\Data\ProductLinkExtensionFactory $productLinkExtensionFactory
     * @param EntryConverterPool $mediaGalleryEntryConverterPool
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $joinProcessor
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurableProductBlock
     * @param \Magento\Catalog\Helper\Image $helperImage
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \Magento\Framework\Locale\FormatInterface $formatInterface
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface
     * @param \Magestore\Webpos\Api\Inventory\StockItemRepositoryInterface $stockItemRepositoryInterFace
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magestore\Webpos\Helper\Data $webposHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProductType
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \Magento\Bundle\Model\Option $bundleOption
     * @param \Magento\Bundle\Model\Product\Type $bundleProductType
     * @param \Magestore\Webpos\Api\Catalog\ProductOptionsRepositoryInterface $productOptionsRepository
     * @param \Magento\Weee\Model\Tax $weeTax
     * @param \Magestore\Webpos\Api\Catalog\ProductSearchRepositoryInterface $productSearchRepository
     * @param PriceCollectionFactory $catalogRuleProductPricecollectionFactory
     * @param GiftCardPriceOptionsInterfaceFactory $giftCardPriceOptionsInterfaceFactory
     * @param GiftCardTemplateInterfaceFactory $giftCardTemplateInterfaceFactory
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $metadataService,
        \Magento\Catalog\Model\Product\Url $url,
        \Magento\Catalog\Model\Product\Link $productLink,
        \Magento\Catalog\Model\Product\Configuration\Item\OptionFactory $itemOptionFactory,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory,
        \Magento\Catalog\Model\Product\OptionFactory $catalogProductOptionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $catalogProductStatus,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Catalog\Model\ResourceModel\Product $resource,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $resourceCollection,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\Catalog\Model\Indexer\Product\Eav\Processor $productEavIndexerProcessor,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Product\Image\CacheFactory $imageCacheFactory,
        \Magento\Catalog\Model\ProductLink\CollectionProvider $entityCollectionProvider,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider,
        \Magento\Catalog\Api\Data\ProductLinkInterfaceFactory $productLinkFactory,
        \Magento\Catalog\Api\Data\ProductLinkExtensionFactory $productLinkExtensionFactory,
        EntryConverterPool $mediaGalleryEntryConverterPool,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $joinProcessor,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct,
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurableProductBlock,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\Locale\FormatInterface $formatInterface,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magestore\Webpos\Api\Inventory\StockItemRepositoryInterface $stockItemRepositoryInterFace,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magestore\Webpos\Helper\Data $webposHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProductType,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \Magento\Bundle\Model\Option $bundleOption,
        \Magento\Bundle\Model\Product\Type $bundleProductType,
        \Magestore\Webpos\Api\Catalog\ProductOptionsRepositoryInterface $productOptionsRepository,
        \Magento\Weee\Model\Tax $weeTax,
        \Magestore\Webpos\Api\Catalog\ProductSearchRepositoryInterface $productSearchRepository,
        PriceCollectionFactory $catalogRuleProductPricecollectionFactory,
        GiftCardPriceOptionsInterfaceFactory $giftCardPriceOptionsInterfaceFactory,
        GiftCardTemplateInterfaceFactory $giftCardTemplateInterfaceFactory,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $storeManager,
            $metadataService,
            $url,
            $productLink,
            $itemOptionFactory,
            $stockItemFactory,
            $catalogProductOptionFactory,
            $catalogProductVisibility,
            $catalogProductStatus,
            $catalogProductMediaConfig,
            $catalogProductType,
            $moduleManager,
            $catalogProduct,
            $resource,
            $resourceCollection,
            $collectionFactory,
            $filesystem,
            $indexerRegistry,
            $productFlatIndexerProcessor,
            $productPriceIndexerProcessor,
            $productEavIndexerProcessor,
            $categoryRepository,
            $imageCacheFactory,
            $entityCollectionProvider,
            $linkTypeProvider,
            $productLinkFactory,
            $productLinkExtensionFactory,
            $mediaGalleryEntryConverterPool,
            $dataObjectHelper,
            $joinProcessor,
            $data
        );
        $this->_configurableProduct = $configurableProduct;
        $this->_configurableProductBlock = $configurableProductBlock;
        $this->_helperImage = $helperImage;
        $this->_emulation = $emulation;
        $this->_formatInterface = $formatInterface;
        $this->_priceCurrencyInterface = $priceCurrencyInterface;
        $this->_stockItemRepositoryInterFace = $stockItemRepositoryInterFace;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_webposHelper = $webposHelper;
        $this->_productFactory = $productFactory;
        $this->_groupedProductType = $groupedProductType;
        $this->_stockRegistryInterface = $stockRegistryInterface;
        $this->_bundleOption = $bundleOption;
        $this->_bundleProductType = $bundleProductType;
        $this->productOptionsRepository = $productOptionsRepository;
        $this->weeTax = $weeTax;
        $this->productSearchRepository = $productSearchRepository;
        $this->catalogRuleProductPricecollectionFactory = $catalogRuleProductPricecollectionFactory;
        $this->giftCardPriceOptionsInterfaceFactory = $giftCardPriceOptionsInterfaceFactory;
        $this->giftCardTemplateInterfaceFactory = $giftCardTemplateInterfaceFactory;
        $this->webposManagement = $webposManagement;
        $this->stockManagement = $stockManagement;
        $this->objectManager = $objectManager;
    }

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var
     */
    protected $_chidrenCollection;

    /**
     * Get product all options
     *
     * @return ProductOptionsInterface
     */
    public function getProductAllOptions()
    {
        if (!$this->_productOptions) {
            if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $this->_productOptions = $this->productOptionsRepository->getProductOptions($this->getId());
            } else {
                $om = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magestore\Webpos\Api\Data\Catalog\Option\ProductOptionsInterface $productOptions */
                $productOptions = $om->create(ProductOptionsInterface::class);
                $productOptions->setConfigOption([]);

                $this->_productOptions = $productOptions;
            }
        }
        return $this->_productOptions;
    }

    /**
     * Get product
     *
     * @return \Magento\Catalog\Model\Product|Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->load($this->getId());
        }
        return $this->_product;
    }

    /**
     * Product short description
     *
     * @return string|null
     */
    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    /**
     * Product description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Get base cost
     *
     * @return float|null
     */
    public function getBaseCost()
    {
        return $this->getData(self::BASE_COST);
    }

    /**
     * Retrieve images
     *
     * @return array/null
     */
    public function getImages()
    {
        $product = $this->getProduct();
        $images = [];
        if (!empty($product->getMediaGallery('images'))) {
            foreach ($product->getMediaGallery('images') as $image) {
                if ((isset($image['disabled']) && $image['disabled']) || empty($image['value_id'])) {
                    continue;
                }
                $images[] = $this->getMediaConfig()->getMediaUrl($image['file']);
            }
        }
        return $images;
    }

    /**
     * Sets product image from it's child if possible
     *
     * @return string
     */
    public function getImage()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->_emulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $imgUrl = $this->_helperImage
            ->init($this, 'category_page_grid')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false)
            ->resize(310, 350)
            ->getUrl();
        if (!$this->_registry->registry('wp_is_search_product') &&
            (!$imgUrl || $imgUrl == $this->_helperImage->getDefaultPlaceholderUrl())
        ) {
            $images = $this->getImages();
            if (count($images)) {
                $imgUrl = $images[0];
            } else {
                $imgUrl = '';
            }
        }
        $this->_emulation->stopEnvironmentEmulation();
        return $imgUrl;
    }

    /**
     * @inheritdoc
     */
    public function getIsOptions()
    {
        if ($this->_registry->registry('wp_is_show_options')) {
            return $this->getProductAllOptions()->getIsOptions();
        } else {
            return 0;
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfigOption()
    {
        if ($this->_registry->registry('wp_is_show_options')) {
            return $this->getProductAllOptions()->getConfigOption();
        }
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        /** if product is configurable */
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $this->_configurableProductBlock->setProduct($this);
            return $this->_configurableProductBlock->getJsonConfig();
        }
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     *
     * Price calculation depending on product options
     *
     * @return string
     */
    public function getPriceConfig()
    {
        if ($this->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return;
        }

        /** @var \Magento\Framework\Json\EncoderInterface $jsonEncoder */
        $jsonEncoder = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Json\EncoderInterface::class
        );
        if (!$this->hasOptions()) {
            $config = [
                'productId' => $this->getId(),
                'priceFormat' => $this->_formatInterface->getPriceFormat()
            ];
            return $jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $tierPricesList = $this->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        if (!empty($tierPricesList)) {
            foreach ($tierPricesList as $tierPrice) {
                $value = ($tierPrice['price']) ? $tierPrice['price']->getValue() : 0;
                $tierPrices[] = $this->_priceCurrencyInterface->convert($value);
            }
        }
        $oldPrice = ($this->getPriceInfo()->getPrice('regular_price')->getAmount()) ?
            $this->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue() : 0;
        $finalPrice = ($this->getPriceInfo()->getPrice('final_price')->getAmount()) ?
            $this->getPriceInfo()->getPrice('final_price')->getAmount()->getValue() : 0;

        $config = [
            'productId' => $this->getId(),
            'priceFormat' => $this->_formatInterface->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->_priceCurrencyInterface->convert(
                        $oldPrice
                    ),
                    'adjustments' => []
                ],
                'basePrice' => [
                    'amount' => $this->_priceCurrencyInterface->convert(
                        $finalPrice
                    ),
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount' => $this->_priceCurrencyInterface->convert(
                        $finalPrice
                    ),
                    'adjustments' => []
                ]
            ],
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        ];

        return $jsonEncoder->encode($config);
    }

    /**
     * Get gift card price config
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\GiftCardPriceOptionsInterface
     */
    public function getGiftCardPriceConfig()
    {
        if (\Magento\Framework\App\ObjectManager::getInstance()->get(\Magestore\Webpos\Helper\Data::class)
            ->isEnabledGiftCard()) {
            if ($this->getTypeId() != \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE) {
                return;
            }
        } else {
            return;
        }
        $giftCardPriceConfig = $this->giftCardPriceOptionsInterfaceFactory->create();
        $giftCardPriceConfig->setGiftCardType($this->getData(GiftCardPriceOptionsInterface::GIFT_CARD_TYPE));
        $giftCardPriceConfig->setGiftType($this->getData(GiftCardPriceOptionsInterface::GIFT_TYPE));
        $giftCardPriceConfig->setGiftValue($this->getData(GiftCardPriceOptionsInterface::GIFT_VALUE));
        $giftCardPriceConfig->setGiftFrom($this->getData(GiftCardPriceOptionsInterface::GIFT_FROM));
        $giftCardPriceConfig->setGiftTo($this->getData(GiftCardPriceOptionsInterface::GIFT_TO));
        $giftCardPriceConfig->setGiftDropdown($this->getData(GiftCardPriceOptionsInterface::GIFT_DROPDOWN));
        $giftCardPriceConfig->setGiftPriceType($this->getData(GiftCardPriceOptionsInterface::GIFT_PRICE_TYPE));
        $giftCardPriceConfig->setGiftPrice($this->getData(GiftCardPriceOptionsInterface::GIFT_PRICE));
        $templateIds = $this->getData('gift_template_ids');
        $templateIdsArray = explode(',', $templateIds);
        $templates = [];
        foreach ($templateIdsArray as $templateId) {
            $giftCardTemplate = $this->giftCardTemplateInterfaceFactory->create();
            $giftCardTemplate->setTemplateId($templateId);
            $templateModel = \Magento\Framework\App\ObjectManager::getInstance()->create(
                \Magestore\Giftvoucher\Model\GiftTemplate::class
            )->load($templateId);
            $imagesUrlArray = [];
            if ($templateModel->getId() && $templateModel->getImages()) {
                $images = $templateModel->getImages();
                $imagesArray = explode(',', $images);
                foreach ($imagesArray as $images) {
                    $imagesUrlArray[] = $images;
                }
                $giftCardTemplate->setImages($imagesUrlArray);
            } else {
                $giftCardTemplate->setImages([]);
            }

            $templates[] = $giftCardTemplate;
        }

        $giftCardPriceConfig->setTemplates($templates);
        return $giftCardPriceConfig;
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getTypeInstance()->hasOptions($this)) {
            return true;
        }

        if ($this->getTypeId() == 'giftvoucher') {
            $giftType = $this->getGiftType();
            if (!$giftType) {
                $giftType = $this->getResource()->getAttributeRawValue(
                    $this->getId(),
                    'gift_type',
                    $this->getStoreId()
                );
            }
            return $giftType != 1;
        }

        if ($this->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return true;
        }

        return false;
    }

    /**
     * Get stock item
     *
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface|mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getStockItem()
    {
        if ($this->getData('stock_item')) {
            return $this->getData('stock_item');
        }
        $stockItem = $this->_stockRegistryInterface->getStockItem($this->getId());

        if ($this->webposManagement->isMSIEnable()) {
            // correct stock qty with MSI
            $stockId = $this->stockManagement->getStockId();
            if ($stockId) {
                /* Get Quantity from Source */
                $sourceCodes = $this->stockManagement->getLinkedSourceCodesByStockId($stockId);
                if (!empty($sourceCodes) && count($sourceCodes)) {
                    if (!$this->isComposite()) {
                        $stockItem->setQuantity($stockItem->getQty());
                    } else {
                        $stockItem->setQuantity(0);
                    }
                }
                /* End */

                $stockTable = $this->objectManager
                    ->get(\Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface::class)
                    ->execute($stockId);
                if ($this->getResource()->getConnection()->isTableExists($stockTable)) {
                    $reservationTable = $this->getResource()->getTable('inventory_reservation');
                    $reservationData = $this->getResource()->getConnection()->select()
                        ->from(['main_table' => $reservationTable], ['sku'])
                        ->where('stock_id = ?', $stockId)
                        ->where('sku = ?', $this->getSku())
                        ->columns(
                            ['quantity' => 'SUM(IF(main_table.quantity, main_table.quantity, 0))']
                        )->group('sku');

                    $stockTable = $this->getResource()->getTable($stockTable);
                    $stockQuantityData = $this->getResource()->getConnection()->select()
                        ->from(['main_table' => $stockTable], ['is_salable'])
                        ->where('sku = ?', $this->getSku())
                        ->columns(
                            ['quantity' => 'SUM(IF(main_table.quantity, main_table.quantity, 0))']
                        )->group('sku');

                    $reservationStock = 0;
                    if ($reservationResult = $this->getResource()->getConnection()->fetchAll($reservationData)) {
                        $reservationStock = (float)$reservationResult[0]['quantity'];
                    }
                    if ($stockQuantityResult = $this->getResource()->getConnection()->fetchAll($stockQuantityData)) {
                        $reservationStock += (float)$stockQuantityResult[0]['quantity'];
                        if (isset($stockQuantityResult[0]['is_salable'])) {
                            $stockItem->setIsInStock((bool) $stockQuantityResult[0]['is_salable']);
                        }
                    }
                    $stockItem->setQty($reservationStock);
                }
            }
        }

        $this->getData('stock_item', $stockItem);
        return $stockItem;
    }

    /**
     * Check product in stock
     *
     * @param int $productId
     * @return bool
     */
    public function checkStock($productId)
    {
        $inStock = true;
        $product = $this->_productFactory->create()->load($productId);
        if (!$product->getId()) {
            $inStock = false;
        } else {
//            $stockData = $this->_stockRegistryInterface->getStockItem($product->getId());
            $stockData = $this->getStockItem();
            if ($stockData->getData('manage_stock') && !$stockData->getData('backorders')
                && ($stockData->getData('qty') <= 0 || !$stockData->getData('is_in_stock'))
            ) {
                $inStock = false;
            }
        }
        return $inStock;
    }

    /**
     * Retrieve assigned category Ids
     *
     * @return string
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $wasLocked = false;
            if ($this->isLockedAttribute('category_ids')) {
                $wasLocked = true;
                $this->unlockAttribute('category_ids');
            }
            //$ids = $this->_getResource()->getCategoryIds($this);
            $ids = $this->getShowedCategoryIds();
            $this->setData('category_ids', $ids);
            if ($wasLocked) {
                $this->lockAttribute('category_ids');
            }
        }

        if (is_array($this->_getData('category_ids')) && count($this->_getData('category_ids'))) {
            $catStrings = '';
            foreach ($this->_getData('category_ids') as $catId) {
                $catStrings .= '\'' . $catId . '\'';
            }
            return $catStrings;
        }

        if (!is_array($this->_getData('category_ids'))) {
            return $this->_getData('category_ids');
        }
    }

    /**
     * Retrieve product tax class id
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->getData(self::TAX_CLASS_ID);
    }

    /**
     * Get attributes to search
     *
     * @return array
     */
    public function getAttributesToSearch()
    {
        return $this->productSearchRepository->getSearchAttributes();
    }

    /**
     * Get search string to search product
     *
     * @return string
     */
    public function getSearchString()
    {
        $searchString = '';
        $attributesToSearch = $this->getAttributesToSearch();
        if (!empty($attributesToSearch)) {
            foreach ($attributesToSearch as $attribute) {
                if ($this->getData($attribute) && !is_array($this->getData($attribute))) {
                    $searchString .= $this->getData($attribute) . ' ';
                }
            }
        }
        $barcodes = $this->getBarCodeByProduct();
        if (!empty($barcodes)) {
            foreach (explode(',', $barcodes) as $barcode) {
                if (!empty($barcode)) {
                    $searchString .= ' ' . $barcode;
                }
            }
        }
        return $searchString;
    }

    /**
     * Get barcode by product
     *
     * @param Product|null $product
     * @return string
     */
    public function getBarCodeByProduct($product = null)
    {
        if (!$product) {
            $product = $this;
        }
        if ($this->getData('barcode_by_product') === null) {
            $barcode = "";
            $barcodeObject = new \Magento\Framework\DataObject();
            $barcodeObject->setBarcode($barcode);
            \Magento\Framework\App\ObjectManager::getInstance()
                ->create(\Magento\Framework\Event\ManagerInterface::class)
                ->dispatch(
                    'webpos_product_get_barcode_after',
                    ['object_barcode' => $barcodeObject, 'product' => $product]
                );
            $barcode = $barcodeObject->getBarcode();
            $this->setData('barcode_by_product', $barcode);
        }
        return $this->getData('barcode_by_product');
    }

    /**
     * Get children collection of configurable product
     *
     * @return \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     */
    public function getChildrenCollection()
    {
        if (!$this->_chidrenCollection) {
            $collection = $this->_configurableProduct->getUsedProductCollection($this);
            $this->_chidrenCollection = $collection;
        }
        return $this->_chidrenCollection;
    }

    /**
     * Get All category ids include anchor categories
     *
     * @return array
     */
    public function getShowedCategoryIds()
    {
        Profiler::start('categories_' . $this->getEntityId());
        $categoryCollection = $this->getCategoryCollection();
        $categoryIds = $categoryCollection->getAllIds();
        $anchorIds = [];
        foreach ($categoryCollection as $category) {
            $pathIds = $category->getPathIds();
            array_pop($pathIds);
            //phpcs:ignore Magento2.Performance.ForeachArrayMerge
            $anchorIds = array_unique(array_merge($anchorIds, $pathIds));
        }
        $anchorCollection = $this->_categoryCollectionFactory->create()
            ->addFieldToFilter('is_anchor', 1)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => $anchorIds]);
        Profiler::stop('categories_' . $this->getEntityId());
        return array_unique(array_merge($categoryIds, $anchorCollection->getAllIds()));
    }

    /**
     * Get is virtual
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsVirtual()
    {
        $virtualTypes = [
            'customercredit', 'virtual'
        ];
        if (in_array($this->getTypeId(), $virtualTypes)) {
            return true;
        }
        return false;
    }

    /**
     * Get product qty increment
     *
     * @return float|null
     */
    public function getQtyIncrement()
    {
        $qtyIncrement = 0;
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        if (is_array($stockData) && array_key_exists('enable_qty_increments', $stockData)
            && $stockData['enable_qty_increments'] == 1) {
            if (array_key_exists('qty_increments', $stockData) && $stockData['qty_increments'] > 0) {
                $qtyIncrement = $stockData['qty_increments'];
            }
        }
        return $qtyIncrement;
    }

    /**
     * Get is in stock
     *
     * @return int
     */
    public function getIsInStock()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $isInStock = $this->getData(self::IS_IN_STOCK);
        if ($isInStock == null) {
            if (array_key_exists(self::IS_IN_STOCK, $stockData)) {
                $isInStock = $stockData[self::IS_IN_STOCK];
            }
        }
        return $isInStock;
    }

    /**
     * Get minimum qty
     *
     * @return float|null
     */
    public function getMinimumQty()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $qty = $this->getData(self::MIN_SALE_QTY);
        if (!$qty) {
            if (array_key_exists(self::MIN_SALE_QTY, $stockData)) {
                $qty = $stockData[self::MIN_SALE_QTY];
            }
        }
        return $qty;
    }

    /**
     * Get maximum float|null
     *
     * @return float|null
     */
    public function getMaximumQty()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $qty = $this->getData(self::MAX_SALE_QTY);
        if (!$qty) {
            if (array_key_exists(self::MAX_SALE_QTY, $stockData)) {
                $qty = $stockData[self::MAX_SALE_QTY];
            }
        }
        return $qty;
    }

    /**
     * Get qty
     *
     * @return string
     */
    public function getQty()
    {
        $qty = $this->getData(self::QTY);
        if (!$qty) {
            $stockItem = null;
            $extendedAttributes = $this->getExtensionAttributes();
            if ($extendedAttributes !== null) {
                $stockItem = $extendedAttributes->getStockItem();
            }
            if (!$stockItem) {
                $product = $this->getProduct();
                $stockItem = $product->getStockItem();
            }
            $stockData = $stockItem->getData();
            if (array_key_exists(self::QTY, $stockData)) {
                $qty = $stockData[self::QTY];
            }
        }
        return $qty;
    }

    /**
     * Get stock data by product
     *
     * @return \Magestore\Webpos\Api\Data\Inventory\StockItemInterface[]|null
     */
    public function getStocks()
    {
        if ($this->_registry->registry('wp_is_show_stock')) {
            $stocks = [];

            $stockItem = $this->getStockItem();
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magestore\Webpos\Api\Data\Inventory\StockItemInterface $item */
            $item = $om->create(\Magestore\Webpos\Api\Data\Inventory\StockItemInterface::class);
            $item->setItemId($stockItem->getItemId());
            $item->setProductId($stockItem->getProductId());
            $item->setStockId($stockItem->getStockId());
            $item->setSku($this->getSku());
            $item->setName($this->getName());
            $item->setQty($stockItem->getQty());
            $item->setIsInStock($stockItem->getIsInStock());
            $item->setUseConfigManageStock($stockItem->getUseConfigManageStock());
            $item->setManageStock($stockItem->getManageStock());
            $item->setBackorders($stockItem->getBackorders());
            $item->setUseConfigBackorders($stockItem->getUseConfigBackorders());
            $item->setMinQty($stockItem->getMinQty());
            $item->setUseConfigMinQty($stockItem->getUseConfigMinQty());
            $item->setMinSaleQty($stockItem->getMinSaleQty());
            $item->setUseConfigMinSaleQty($stockItem->getUseConfigMinSaleQty());
            $item->setMaxSaleQty($stockItem->getMaxSaleQty());
            $item->setUseConfigMaxSaleQty($stockItem->getUseConfigMaxSaleQty());
            $item->setUpdatedTime($this->getUpdatedAt());
            $item->setUseConfigQtyIncrements($stockItem->getUseConfigQtyIncrements());
            $item->setQtyIncrements($stockItem->getQtyIncrements());
            $item->setIsQtyDecimal($stockItem->getIsQtyDecimal());
            $item->setUseConfigEnableQtyInc($stockItem->getUseConfigEnableQtyInc());
            $item->setEnableQtyIncrements($stockItem->getEnableQtyIncrements());
            $item->setQuantity($stockItem->getQuantity());

            $stocks[] = $item;

//            $productId = ($this->getProduct()) ? $this->getProduct()->getId() : false;
//            Profiler::start('stocks_' . $productId);
//            if ($productId) {
//                $this->_searchCriteriaBuilder->addFilter('e.entity_id', $productId);
//                $searchCriteria = $this->_searchCriteriaBuilder->create();
//                $stockItems = $this->_stockItemRepositoryInterFace->getStockItems($searchCriteria);
//                $stocks = $stockItems->getItems();
//            }
//            Profiler::stop('stocks_' . $productId);
            return $stocks;
        }
        return null;
    }

    /**
     * Get is in stock
     *
     * @return int
     */
    public function getBackorders()
    {
        return $this->getData('backorders');
    }

    /**
     * Get final price
     *
     * @param float|null $qty
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFinalPrice($qty = null)
    {
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $rate = 1;
            if ($this->_storeManager->getStore()->getCurrentCurrencyRate()) {
                $rate = $this->_storeManager->getStore()->getCurrentCurrencyRate();
            }
            $priceConverted = $this->getPriceModel()->getFinalPrice($qty, $this) / $rate;
            return $priceConverted;
        }
        return parent::getFinalPrice($qty);
    }

    /**
     * Get qty increments
     *
     * @return string
     */
    public function getQtyIncrements()
    {
        return $this->getData(self::QTY_INCREMENTS);
    }

    /**
     * Get enable qty increments
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getEnableQtyIncrements()
    {
        return $this->getData(self::ENABLE_QTY_INCREMENTS);
    }

    /**
     * Get is qty decimal
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsQtyDecimal()
    {
        $product = $this->getProduct();
        $stockItem = $product->getStockItem();
        $extendedAttributes = $this->getExtensionAttributes();
        if ($extendedAttributes !== null) {
            $stockItem = $extendedAttributes->getStockItem();
        }
        $stockData = $stockItem->getData();
        $isQtyDecimal = $this->getData(self::IS_QTY_DECIMAL);
        if (!$isQtyDecimal) {
            if (array_key_exists(self::IS_QTY_DECIMAL, $stockData)) {
                $isQtyDecimal = $stockData[self::IS_QTY_DECIMAL];
            }
        }
        return $isQtyDecimal;
    }

    /**
     * Get product price through type instance
     *
     * @return float
     */
    public function getPrice()
    {
        if ($this->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE
            && $this->_registry->registry('wp_is_search_product')
        ) {
            if ($this->_calculatePrice || !$this->getData(self::PRICE)) {
                $product = $this->_productFactory->create();
                $product->setData($this->getData());
                return $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            }
        }
        return parent::getPrice();
    }

    /**
     * Get data of children product
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductOriginalInterface[]|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getChildrenProducts()
    {
        if ($this->_registry->registry('wp_is_search_product')) {
            return null;
        }
        if ($this->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            if ($this->_registry->registry('webpos_get_product_by_id')) {
                return null;
            }
            $collection = $this->getChildrenCollection()->addAttributeToSelect(
                ['price', 'name', 'status', 'tax_class_id', 'special_price', 'special_from_date', 'special_to_date']
            );
            return $this->getChildrenProductsData($collection);
        } elseif ($this->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $items = [];
            $productSKUs = [];
            $bunleOptions = $this->getExtensionAttributes()->getBundleProductOptions();
            if ($bunleOptions && !empty($bunleOptions)) {
                foreach ($bunleOptions as $_option) {
                    $productLinks = $_option->getProductLinks();
                    foreach ($productLinks as $_productLink) {
                        $productSKUs[] = $_productLink->getSku();
                    }
                }
                $collection = $this->getCollection()
                    ->addAttributeToFilter('sku', ['in' => $productSKUs])
                    ->addAttributeToSelect(
                        [
                            'price',
                            'name',
                            'status',
                            'tax_class_id',
                            'special_price',
                            'special_from_date',
                            'special_to_date'
                        ]
                    );
                return $this->getChildrenProductsData($collection);
            }
            return $items;
        } elseif ($this->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $collection = $this->getTypeInstance()->getAssociatedProducts($this);
            return $this->getChildrenProductsData($collection);
        }
        return null;
    }

    /**
     * Get children products data
     *
     * @param array $collection
     * @return array
     */
    public function getChildrenProductsData($collection)
    {
        $items = [];
        $isSearchProduct = $this->_registry->registry('wp_is_search_product')
            || $this->_registry->registry('webpos_get_product_by_id');
        $isPriceTypeDynamic = $this->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE ||
            $this->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC;
        foreach ($collection as $item) {
            $item->load($item->getId());
            $image = $item->getImage();
            if ($image) {
                $item->setImage($this->getMediaConfig()->getMediaUrl($image));
            }
            $fpt = $this->getChildrenFpt($item);
            if ($fpt) {
                $item->setFpt($fpt);
            }
            if ($isSearchProduct && $isPriceTypeDynamic) {
                $catalogRulePrices = $this->getCatalogruleChildrenPrices($item);
                $item->setCatalogrulePrices($catalogRulePrices);
            }
            $items[] = $item;
        }
        return $items;
    }

    /**
     * Get qty in online mode
     *
     * @return float|null
     */
    public function getQtyOnline()
    {
        return $this->getQty();
    }

    /**
     * Get children FPT
     *
     * @param Product $child
     * @return array
     */
    public function getChildrenFpt($child)
    {
        $ftp = [];
        $weeTax = $this->weeTax->getWeeeTaxAttributeCodes();
        if (count($weeTax)) {
            foreach ($weeTax as $attribute) {
                $ftp[] = $child->getData($attribute);
            }
        }
        return $ftp;
    }

    /**
     * Get FPT
     *
     * @return array
     */
    public function getFpt()
    {
        $ftp = [];
        $weeTax = $this->weeTax->getWeeeTaxAttributeCodes();
        if (count($weeTax)) {
            foreach ($weeTax as $attribute) {
                $ftp[] = $this->getData($attribute);
            }
        }
        return $ftp;
    }

    /**
     * Get shipment type
     *
     * @return int|null
     */
    public function getShipmentType()
    {
        if ($this->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            return $this->getData(self::SHIPMENT_TYPE);
        }
        return null;
    }

    /**
     * Get Price Type
     *
     * @return int|null
     */
    public function getPriceType()
    {
        return $this->getData(self::PRICE_TYPE);
    }

    /**
     * Get list of product options
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]|null
     */
    public function getCustomOptions()
    {
        return $this->getProduct()->getOptions();
    }

    /**
     * Get product weight type
     *
     * @return int
     */
    public function getWeightType()
    {
        return $this->getData(self::WEIGHT_TYPE);
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * Get Pos barcode
     *
     * @return string
     */
    public function getPosBarcode()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get(\Magestore\Webpos\Helper\Data::class);
        $searchAttribute = $helper->getStoreConfig('webpos/product_search/barcode');
        $posBarcode = $this->getData($searchAttribute);
        $barcodes = $this->getBarCodeByProduct();
        if (!empty($barcodes)) {
            foreach (explode(',', $barcodes) as $barcode) {
                if (!empty($barcode)) {
                    $posBarcode .= ',' . $barcode;
                }
            }
        }
        $posBarcode = ',' . $posBarcode . ',';
        return $posBarcode;
    }

    /**
     * Get children special price
     *
     * @param Product $child
     * @return float
     */
    public function getChildrenSpecialPrice($child)
    {
        return $child->getSpecialPrice();
    }

    /**
     * Get catalog rule product prices
     *
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface[]|null
     */
    public function getCatalogrulePrices()
    {
        if ($this->_registry->registry('wp_is_search_product')
            || $this->_registry->registry('webpos_get_product_by_id')
        ) {
            /** @var \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\Collection $collection */
            $collection = $this->catalogRuleProductPricecollectionFactory->create();
            $collection->addFieldToFilter(
                \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface::PRODUCT_ID,
                $this->getId()
            );
            return $collection->getItems();
        }
        return null;
    }

    /**
     * Get catalog rule product prices
     *
     * @param \Magento\Framework\DataObject $children
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface[]
     */
    public function getCatalogruleChildrenPrices($children)
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\Collection $collection */
        $collection = $this->catalogRuleProductPricecollectionFactory->create();
        $collection->addFieldToFilter(
            \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface::PRODUCT_ID,
            $children->getId()
        );
        return $collection->getItems();
    }
}
