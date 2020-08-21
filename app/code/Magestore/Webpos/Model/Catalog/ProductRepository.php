<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Catalog;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use \Magento\CatalogInventory\Model\Stock as Stock;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DB\Adapter\ConnectionException;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magestore\Webpos\Api\Catalog\ProductRepositoryInterface;

/**
 * Catalog ProductRepository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductRepository extends \Magento\Catalog\Model\ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Catalog\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManagerInterFace;

    /**
     * @var Product
     */
    protected $_webposProduct;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection
     */
    protected $_productCollection;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Weee\Model\Tax
     */
    protected $weeTax;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    protected $listAttributes = [
        'entity_id',
        'type_id',
        'category_ids',
        'description',
        'has_options',
        'image',
        'small_image',
        'name',
        'price',
        'sku',
        'special_from_date',
        'special_to_date',
        'special_price',
        'status',
        'tax_class_id',
        'tier_price',
        'updated_at',
        'weight'
    ];

    protected $listCondition = [
        'eq' => '=',
        'neq' => '!=',
        'like' => 'like',
        'gt' => '>',
        'gteq' => '>=',
        'lt' => '<',
        'lteq' => '<=',
        'in' => 'in'
    ];

    protected $collectionSize = 0;

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        $this->registry = $objectManager->get(\Magento\Framework\Registry::class);
        $this->registry->register('webpos_get_product_list', true);
        $isShowStock = (boolean)$this->request->getParam('show_stock');
        $isShowOption = (boolean)$this->request->getParam('show_option');
        if ($isShowOption) {
            $this->registry->register('wp_is_show_options', true);
        } else {
            $this->registry->register('wp_is_show_options', false);
        }
        if ($isShowStock) {
            $this->registry->register('wp_is_show_stock', true);
        } else {
            $this->registry->register('wp_is_show_stock', false);
        }
        /** @var \Magestore\Webpos\Helper\Data $helper */
        $helper = $objectManager->get(\Magestore\Webpos\Helper\Data::class);
        $storeId = $helper->getCurrentStoreView()->getId();
        $this->prepareCollection($searchCriteria);
        $this->_productCollection->setStoreId($storeId);
        $moduleManager = $objectManager->get(\Magento\Framework\Module\Manager::class);
        if (!$moduleManager->isEnabled('Magestore_InventorySuccess')) {
            $this->_productCollection->addStoreFilter($storeId);
        }
        $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
        $this->_productCollection->setPageSize($searchCriteria->getPageSize());
        $searchResult = $this->searchResultsFactory->create();
        $collectionSize = $this->_productCollection->getSize();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($this->_productCollection->getItems());
        $searchResult->setTotalCount($collectionSize);
        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function sync(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Framework\App\Cache\StateInterface $cacheState */
        $cacheState = $objectManager->get(\Magento\Framework\App\Cache\StateInterface::class);
        if (!$cacheState->isEnabled(\Magestore\Webpos\Model\Cache\Type::TYPE_IDENTIFIER)
            || count($searchCriteria->getFilterGroups())) {
            return $this->getList($searchCriteria);
        }

        /** @var \Magento\Framework\App\CacheInterface $cache */
        $cache = $objectManager->get(\Magento\Framework\App\CacheInterface::class);

        /** @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository */
        $sessionRepository = $objectManager->get(\Magestore\Webpos\Api\Staff\SessionRepositoryInterface::class);
        $this->request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        $session = $sessionRepository->getBySessionId(
            $this->request->getParam(
                \Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY
            )
        );
        $locationId = $session->getLocationId();

        $this->_moduleManager = $objectManager->get(\Magento\Framework\Module\Manager::class);

        /** @var \Magestore\Webpos\Api\WebposManagementInterface $webposManagement */
        $webposManagement = $objectManager->get(\Magestore\Webpos\Api\WebposManagementInterface::class);
        if ($webposManagement->isMSIEnable()) {
            /** @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement */
            $stockManagement = $objectManager->get(
                \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface::class
            );
            $locationId = $stockManagement->getStockId();
        }

        /** @var \Magestore\Webpos\Helper\Data $helper */
        $helper = $objectManager->get(\Magestore\Webpos\Helper\Data::class);
        $key = 'syncProducts-' . $searchCriteria->getPageSize() . '-'
            . $searchCriteria->getCurrentPage() . '-'
            . $locationId . '-'
            . $helper->getCurrentStoreView()->getId();

        if ($response = $cache->load($key)) {
            return json_decode($response, true);
        }

        // Check async queue for product
        $flag = \Magestore\Webpos\Api\SyncInterface::QUEUE_NAME;
        if ($cachedAt = $cache->load($flag)) {
            return [
                'async_stage' => 2, // processing
                'cached_at' => $cachedAt, // process started time
            ];
        }

        // Block metaphor
        $cachedAt = date("Y-m-d H:i:s");
        $cache->save($cachedAt, $flag, [\Magestore\Webpos\Model\Cache\Type::CACHE_TAG], 300);

        /** @var \Magento\Framework\Webapi\ServiceOutputProcessor $processor */
        $processor = $objectManager->get(\Magento\Framework\Webapi\ServiceOutputProcessor::class);
        $outputData = $processor->process(
            $this->getList($searchCriteria),
            ProductRepositoryInterface::class,
            'sync'
        );
        $outputData['cached_at'] = $cachedAt;
        $cache->save(
            json_encode($outputData),
            $key,
            [
                \Magestore\Webpos\Model\Cache\Type::CACHE_TAG,
            ],
            86400   // Cache lifetime: 1 day
        );
        // Release metaphor
        $cache->remove($flag);
        return $outputData;
    }

    /**
     * @inheritdoc
     */
    public function getProductsWithoutOptions(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_moduleManager = $objectManager->get(\Magento\Framework\Module\Manager::class);
        $this->_productCollection = $objectManager->create(
            \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::class
        );
        $this->prepareCollection($searchCriteria);
        /** @var \Magestore\Webpos\Helper\Data $helper */
        $helper = $objectManager->get(\Magestore\Webpos\Helper\Data::class);
        $storeId = $helper->getCurrentStoreView()->getId();
        $this->_productCollection->setStoreId($storeId);
        $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
        $this->_productCollection->setPageSize($searchCriteria->getPageSize());
        $this->_productCollection->addAttributeToSelect($this->listAttributes);
        if (!$this->_moduleManager->isEnabled('Magestore_InventorySuccess')) {
            $this->_productCollection->addStoreFilter($storeId);
            $this->_productCollection->getSelect()->joinLeft(
                ['stock_item' => $this->_productCollection->getTable('cataloginventory_stock_item')],
                'e.entity_id = stock_item.product_id AND stock_item.stock_id = "' . Stock::DEFAULT_STOCK_ID . '"',
                [
                    'qty',
                    'manage_stock',
                    'backorders',
                    'min_sale_qty',
                    'max_sale_qty',
                    'is_in_stock',
                    'enable_qty_increments',
                    'qty_increments',
                    'is_qty_decimal'
                ]
            );
        }
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($this->_productCollection->getSize());
        $searchResult->setItems($this->_productCollection->getItems());
        return $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function prepareCollection($searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository */
        $sessionRepository = $objectManager->get(\Magestore\Webpos\Api\Staff\SessionRepositoryInterface::class);
        $session = $sessionRepository->getBySessionId(
            $this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY)
        );

        $this->weeTax = $objectManager->create(\Magento\Weee\Model\Tax::class);
        if (empty($this->_productCollection)) {
            $this->_productCollection = $objectManager->create(
                \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::class
            );
            $this->_eventManagerInterFace = $objectManager->get(\Magento\Framework\Event\ManagerInterface::class);
            $this->_eventManagerInterFace->dispatch(
                'webpos_catalog_product_getlist',
                ['collection' => $this->_productCollection, 'is_new' => true, 'location' => $session->getLocationId()]
            );
            /** End integrate webpos **/

            $this->extensionAttributesJoinProcessor->process($this->_productCollection);
            $this->_productCollection->addAttributeToSelect($this->listAttributes);
            $weeTax = $this->weeTax->getWeeeTaxAttributeCodes();
            if (count($weeTax)) {
                $this->_productCollection->addAttributeToSelect($weeTax);
            }
            $this->_productCollection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner'
            );
            $this->_productCollection->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            );
            $this->_productCollection->addVisibleFilter(); // filter visible on pos
            foreach ($searchCriteria->getFilterGroups() as $group) {
                $this->addFilterGroupToCollection($group, $this->_productCollection);
            }
            /** @var SortOrder $sortOrder */
            foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $this->_productCollection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
            /** @var \Magestore\Webpos\Helper\Data $webposHelper */
            $webposHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magestore\Webpos\Helper\Data::class);
            $productTypeIds = $webposHelper->getProductTypeIds();
            $this->_productCollection->addAttributeToFilter('type_id', ['in' => $productTypeIds]);

            $this->filterProductByStockAndSource($this->_productCollection);
        }
    }

    /**
     * Get product attributes to select
     *
     * @return array
     */
    public function getSelectProductAtrributes()
    {
        return [
            self::TYPE_ID,
            self::NAME,
            self::PRICE,
            self::SPECIAL_PRICE,
            self::SPECIAL_FROM_DATE,
            self::SPECIAL_TO_DATE,
            self::SKU,
            self::SHORT_DESCRIPTION,
            self::DESCRIPTION,
            self::IMAGE,
            self::FINAL_PRICE
        ];
    }

    /**
     * Get product type ids to support
     *
     * @return array
     * @deprecated Moved to \Magestore\Webpos\Helper\Data
     */
    public function getProductTypeIds()
    {
        $types = [
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
        ];
        /** @var \Magestore\Webpos\Helper\Data $webposHelper */
        $webposHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magestore\Webpos\Helper\Data::class);
        if ($webposHelper->isEnabledGiftCard()) {
            $types[] = \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE;
        }
        return $types;
    }

    /**
     * Get info about product by product SKU
     *
     * @param string $id
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id, $editMode = false, $storeId = null, $forceReload = false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_webposProduct = $objectManager->get(\Magestore\Webpos\Model\Catalog\Product::class);
        $this->registry = $objectManager->get(\Magento\Framework\Registry::class);
        $this->registry->register('webpos_get_product_by_id', true);
        $this->registry->register('wp_is_show_stock', true);
        $this->registry->register('wp_is_show_options', true);
        $cacheKey = $this->getCacheKey([$editMode, $storeId]);
        if (!isset($this->instancesById[$id][$cacheKey]) || $forceReload) {
            $product = $this->_webposProduct;
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            if ($storeId !== null) {
                $product->setData('store_id', $storeId);
            }
            $product->load($id);
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested product doesn\'t exist'));
            }
            $this->instancesById[$id][$cacheKey] = $product;
            $this->instances[$product->getSku()][$cacheKey] = $product;
        }
        return $this->instancesById[$id][$cacheKey];
    }

    /**
     * Get product options
     *
     * @param string $id
     * @param bool $editMode
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptions($id, $editMode = false, $storeId = null)
    {
        $product = $this->getProductById($id, $editMode, $storeId);
        $data = [];
        $data['custom_options'] = $this->getCustomOptions($product);
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $data['bundle_options'] = $product->getBundleOptions();
        }
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $data['configurable_options'] = $product->getConfigOptions();
            /*$data['json_config'] = $product->getJsonConfig();*/
            $data['price_config'] = $product->getPriceConfig();
        }
        /** @var \Magestore\Webpos\Helper\Data $webposHelper */
        $webposHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magestore\Webpos\Helper\Data::class);
        if ($webposHelper->isEnabledGiftCard()) {
            if ($product->getTypeId() == \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE) {
                $data['gift_card_price_config'] = $product->getGiftCardPriceConfig();
            }
        }
        return \Zend_Json::encode($data);
    }

    /**
     * Get custom options
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\ProductInterface $product
     * @return array
     */
    public function getCustomOptions($product)
    {
        $customOptions = $product->getOptions();
        $options = [];
        foreach ($customOptions as $child) {
            $values = [];
            if ($child->getValues()) {
                foreach ($child->getValues() as $value) {
                    $values[] = $value->getData();
                }
                $child['values'] = $values;
            }
            $options[] = $child->getData();
        }
        return $options;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $fields = [];
        $categoryFilter = [];
        $searchString = '';
        foreach ($filterGroup->getFilters() as $filter) {
            $value = $filter->getValue();
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            if ($filter->getField() == 'category_id') {
                $categoryFilter['in'][] = str_replace("%", "", $value);
                continue;
            }
            $fields[] = ['attribute' => $filter->getField(), $conditionType => $value];
            $searchString = $value ? $value : $searchString;
        }
        if ($categoryFilter && empty($fields)) {
            $collection->addCategoriesFilter($categoryFilter);
        }
        if ($fields) {
            $collection->addAttributeToFilter($fields, '', 'left');
        }
    }

    /**
     * Process product links, creating new links, updating and deleting existing links
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $newLinks
     * @return $this
     * @throws NoSuchEntityException
     */
    private function processLinks(\Magento\Catalog\Api\Data\ProductInterface $product, $newLinks)
    {
        if ($newLinks === null) {
            // If product links were not specified, don't do anything
            return $this;
        }

        // Clear all existing product links and then set the ones we want
        $linkTypes = $this->linkTypeProvider->getLinkTypes();
        foreach (array_keys($linkTypes) as $typeName) {
            $this->linkInitializer->initializeLinks($product, [$typeName => []]);
        }

        // Set each linktype info
        if (!empty($newLinks)) {
            $productLinks = [];
            foreach ($newLinks as $link) {
                $productLinks[$link->getLinkType()][] = $link;
            }

            foreach ($productLinks as $type => $linksByType) {
                $assignedSkuList = [];
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $link */
                foreach ($linksByType as $link) {
                    $assignedSkuList[] = $link->getLinkedProductSku();
                }
                $linkedProductIds = $this->resourceModel->getProductsIdsBySkus($assignedSkuList);

                $linksToInitialize = [];
                foreach ($linksByType as $link) {
                    $linkDataArray = $this->extensibleDataObjectConverter
                        ->toNestedArray($link, [], \Magento\Catalog\Api\Data\ProductLinkInterface::class);
                    $linkedSku = $link->getLinkedProductSku();
                    if (!isset($linkedProductIds[$linkedSku])) {
                        throw new NoSuchEntityException(
                            __('Product with SKU "%1" does not exist', $linkedSku)
                        );
                    }
                    $linkDataArray['product_id'] = $linkedProductIds[$linkedSku];
                    $linksToInitialize[$linkedProductIds[$linkedSku]] = $linkDataArray;
                }

                $this->linkInitializer->initializeLinks($product, [$type => $linksToInitialize]);
            }
        }

        $product->setProductLinks($newLinks);
        return $this;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(\Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false)
    {
        $tierPrices = $product->getData('tier_price');

        try {
            $existingProduct = $this->get($product->getSku());

            $product->setData(
                $this->resourceModel->getLinkField(),
                $existingProduct->getData($this->resourceModel->getLinkField())
            );
            if (!$product->hasData(Product::STATUS)) {
                $product->setStatus($existingProduct->getStatus());
            }
        } catch (NoSuchEntityException $e) {
            $existingProduct = null;
        }

        $productDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray($product, [], \Magento\Catalog\Api\Data\ProductInterface::class);
        $productDataArray = array_replace($productDataArray, $product->getData());
        $ignoreLinksFlag = $product->getData('ignore_links_flag');
        $productLinks = null;
        if (!$ignoreLinksFlag && $ignoreLinksFlag !== null) {
            $productLinks = $product->getProductLinks();
        }
        $productDataArray['store_id'] = (int)$this->storeManager->getStore()->getId();
        $product = $this->initializeProductData($productDataArray, empty($existingProduct));

        $this->processLinks($product, $productLinks);
        if (isset($productDataArray['media_gallery'])) {
            $this->processMediaGallery($product, $productDataArray['media_gallery']['images']);
        }

        if (!$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
        }

        $validationResult = $this->resourceModel->validate($product);
        if (true !== $validationResult) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Invalid product data: %1', implode(',', $validationResult))
            );
        }

        try {
            if ($tierPrices !== null) {
                $product->setData('tier_price', $tierPrices);
            }
            unset($this->instances[$product->getSku()]);
            unset($this->instancesById[$product->getId()]);
            $this->resourceModel->save($product);
        } catch (ConnectionException $exception) {
            throw new \Magento\Framework\Exception\TemporaryState\CouldNotSaveException(
                __('Database connection error'),
                $exception,
                $exception->getCode()
            );
        } catch (DeadlockException $exception) {
            throw new \Magento\Framework\Exception\TemporaryState\CouldNotSaveException(
                __('Database deadlock found when trying to get lock'),
                $exception,
                $exception->getCode()
            );
        } catch (LockWaitException $exception) {
            throw new \Magento\Framework\Exception\TemporaryState\CouldNotSaveException(
                __('Database lock wait timeout exceeded'),
                $exception,
                $exception->getCode()
            );
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $exception) {
            throw \Magento\Framework\Exception\InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $product->getData($exception->getAttributeCode()),
                $exception
            );
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save product'), $e);
        }
        unset($this->instances[$product->getSku()]);
        unset($this->instancesById[$product->getId()]);
        return $this->get($product->getSku(), false, $product->getStoreId());
    }

    /**
     * Create
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Magento\Eav\Model\Entity\Attribute\Exception
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\TemporaryState\CouldNotSaveException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(\Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false)
    {
        /** @var \Magestore\Webpos\Model\Catalog\Product $product */
        $tierPrices = $product->getData('tier_price');
        $productDataArray = $this->extensibleDataObjectConverter
            ->toNestedArray($product, [], \Magento\Catalog\Api\Data\ProductInterface::class);
        $productDataArray = array_replace($productDataArray, $product->getData());
        $ignoreLinksFlag = $product->getData('ignore_links_flag');
        $productLinks = null;
        if (!$ignoreLinksFlag && $ignoreLinksFlag !== null) {
            $productLinks = $product->getProductLinks();
        }
        $productDataArray['store_id'] = (int)$this->storeManager->getStore()->getId();
        $product = $this->initializeProductData($productDataArray, true);

        $this->processLinks($product, $productLinks);
        if (isset($productDataArray['media_gallery'])) {
            $this->processMediaGallery($product, $productDataArray['media_gallery']['images']);
        }

        if (!$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
        }

        $validationResult = $this->resourceModel->validate($product);
        if (true !== $validationResult) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Invalid product data: %1', implode(',', $validationResult))
            );
        }

        try {
            if ($tierPrices !== null) {
                $product->setData('tier_price', $tierPrices);
            }
            unset($this->instances[$product->getSku()]);
            unset($this->instancesById[$product->getId()]);
            $this->resourceModel->save($product);
        } catch (ConnectionException $exception) {
            throw new \Magento\Framework\Exception\TemporaryState\CouldNotSaveException(
                __('Database connection error'),
                $exception,
                $exception->getCode()
            );
        } catch (DeadlockException $exception) {
            throw new \Magento\Framework\Exception\TemporaryState\CouldNotSaveException(
                __('Database deadlock found when trying to get lock'),
                $exception,
                $exception->getCode()
            );
        } catch (LockWaitException $exception) {
            throw new \Magento\Framework\Exception\TemporaryState\CouldNotSaveException(
                __('Database lock wait timeout exceeded'),
                $exception,
                $exception->getCode()
            );
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $exception) {
            throw \Magento\Framework\Exception\InputException::invalidFieldValue(
                $exception->getAttributeCode(),
                $product->getData($exception->getAttributeCode()),
                $exception
            );
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save product'), $e);
        }
        unset($this->instances[$product->getSku()]);
        unset($this->instancesById[$product->getId()]);
        return $this->get($product->getSku(), false, $product->getStoreId());
    }

    /**
     * Filter Product By Stock And Source
     *
     * @param \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection $collection
     */
    public function filterProductByStockAndSource($collection)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement */
        $stockManagement = $objectManager->get(
            \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface::class
        );
        $stockId = $stockManagement->getStockId();
        if ($stockId) {
            $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
            $stockTable = $objectManager->get(
                \Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface::class
            )->execute($stockId);
            $sourceItemTable = $resource->getTableName('inventory_source_item');
            $linkedSources = $stockManagement->getLinkedSourceCodesByStockId($stockId);
            $collection->getSelect()
                ->joinLeft(
                    ['stock_table' => $stockTable],
                    'e.sku = stock_table.sku',
                    ['is_salable']
                )
                ->joinLeft(
                    ['inventory_source_item' => $sourceItemTable],
                    "e.sku = inventory_source_item.sku
                    AND inventory_source_item.source_code IN ('" . implode("', '", $linkedSources) . "')",
                    ['source_code']
                )->group('e.entity_id')
                ->having('inventory_source_item.source_code IN (?)', $linkedSources)
                ->orHaving('stock_table.is_salable = ?', 1);
        }
    }
}
