<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Catalog;

use Magestore\Webpos\Helper\Profiler;

/**
 * Class ProductSearchRepository
 *
 * Used for search product
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductSearchRepository extends ProductRepository implements
    \Magestore\Webpos\Api\Catalog\ProductSearchRepositoryInterface
{
    const SEARCH_ATTRIBUTES = [
        'name',
        'sku',
    ];

    /**
     * Get webpos search attributes
     *
     * @return array
     */
    public function getSearchAttributes()
    {
        $searchAttrs = self::SEARCH_ATTRIBUTES;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->scopeConfig = $objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $barcodeAttr = $this->scopeConfig->getValue('webpos/product_search/barcode');

        if ($barcodeAttr && !in_array($barcodeAttr, $searchAttrs)) {
            $searchAttrs[] = $barcodeAttr;
        }

        return $searchAttrs;
    }

    /**
     * @inheritdoc
     */
    public function barcode(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->search($searchCriteria, true);
    }

    /**
     * Process Search In Elastic
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @param bool $barcode
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processSearchInElasticSearch(
        \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria,
        $barcode = false
    ) {
        if ($searchCriteria->getQueryString() && !$barcode) {
            return $this->searchByElasticSearch($searchCriteria);
        } elseif ($searchCriteria->getQueryString() && $barcode) {
            return $this->scanBarcodeInElasticSearch($searchCriteria);
        } else {
            return $this->getProductListByElasticSearch($searchCriteria);
        }
    }

    /**
     * @inheritdoc
     */
    public function search(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria, $barcode = false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->registry = $objectManager->get(\Magento\Framework\Registry::class);
        $this->registry->register('webpos_get_product_list', true);
        $this->request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        // Show stock by default
        $this->registry->register('wp_is_show_stock', true);
        $this->registry->register('wp_is_show_options', (boolean)$this->request->getParam('show_option'));
        $this->registry->register('wp_is_search_product', true);

        /* @var \Magestore\Webpos\Helper\Data $webposHelper */
        $webposHelper = $objectManager->get(\Magestore\Webpos\Helper\Data::class);
        if ($webposHelper->isEnableElasticSearch()) {
            return $this->processSearchInElasticSearch($searchCriteria, $barcode);
        } else {
            Profiler::start('search');

            $this->prepareCollection($searchCriteria, $barcode);
            $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
            $this->_productCollection->setPageSize($searchCriteria->getPageSize());
            $searchResult = $this->searchResultsFactory->create();
            $searchResult->setSearchCriteria($searchCriteria);

            Profiler::start('load');
            $totalCount = $this->_productCollection->getSize();
            $this->_productCollection->load();
            Profiler::stop('load');

            Profiler::start('items');
            $items = $this->_productCollection->getItems();

            if ($barcode) {
                foreach ($items as $product) {
                    Profiler::start('get_product_' . $product->getEntityId());
                    $product->getProduct();
                    Profiler::stop('get_product_' . $product->getEntityId());
                }
            } elseif (count($items)
                && $this->_productCollection instanceof \Magestore\Webpos\Model\ResourceModel\Catalog\Search\Collection
            ) {
                // Reinit collection from base data
                $collection = $objectManager
                    ->create(\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::class);
                $collection->setStoreId($this->storeManager->getStore()->getId());
                $collection->addAttributeToSelect($this->listAttributes);
                $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
                $collection->addAttributeToSort('name', 'ASC');
                $productIds = [];
                foreach ($items as $product) {
                    $productIds[] = $product->getId();
                }
                $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
                Profiler::start('product_from_eav');
                $items = $collection->getItems();
                Profiler::stop('product_from_eav');
            }
            $searchResult->setItems($items);
            Profiler::stop('items');
            Profiler::start('get_size');
            $searchResult->setTotalCount($totalCount);
            Profiler::stop('get_size');

            Profiler::stop('search');

            return $searchResult;
        }
    }

    /**
     * Check can use indexed table for searching
     *
     * @return boolean
     */
    public function useIndexedTable()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Model\Indexer\Product\Processor $processor */
        $processor = $objectManager->get(\Magestore\Webpos\Model\Indexer\Product\Processor::class);
        return $processor->getIndexer()->isValid();
    }

    /**
     * Search by elastic search
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function searchByElasticSearch(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $searchResultSearchEngine = $objectManager
            ->create(\Magestore\Webpos\Model\ResourceModel\Catalog\Search\Collection::class)
            ->searchWebposProductInSearchEngine($searchCriteria);
        return $this->getListProductFromElasticSearchResult($searchResultSearchEngine, $searchCriteria);
    }

    /**
     * Search by elastic search
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductListByElasticSearch(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $searchResultSearchEngine = $objectManager
            ->create(\Magestore\Webpos\Model\ResourceModel\Catalog\Search\Collection::class)
            ->getProductListInSearchEngine($searchCriteria);
        return $this->getListProductFromElasticSearchResult($searchResultSearchEngine, $searchCriteria);
    }

    /**
     * Get List Product From Elastic Search Result
     *
     * @param \Magento\Framework\Api\Search\SearchResultInterface $searchResultSearchEngine
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getListProductFromElasticSearchResult(
        $searchResultSearchEngine,
        \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productIds = [];
        foreach ($searchResultSearchEngine->getItems() as $item) {
            $productIds[] = $item->getId();
        }
        if (count($productIds)) {
            // Reinit collection from base data
            $collection = $objectManager
                ->create(\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::class);
            $collection->setStoreId($this->storeManager->getStore()->getId());
            $collection->addAttributeToSelect($this->listAttributes);
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            $collection->getSelect()
                ->order(new \Zend_Db_Expr('FIELD(e.entity_id,' . implode(',', $productIds).')'));
            $items = $collection->getItems();
        } else {
            $items = [];
        }

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($searchResultSearchEngine->getTotalCount());
        return $searchResult;
    }

    /**
     * Scan barcode In Elastic
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function scanBarcodeInElasticSearch(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $searchResultSearchEngine = $objectManager
            ->create(\Magestore\Webpos\Model\ResourceModel\Catalog\Search\Collection::class)
            ->getProductIdByBarcodeInElasticSearch($searchCriteria);

        if (count($searchResultSearchEngine->getItems())) {
            $itemList = $searchResultSearchEngine->getItems();
            $itemSearchResult = $itemList[0];
            $productId = $itemSearchResult->getId();
        } else {
            $productId = 0;
        }

        $searchResult = $this->searchResultsFactory->create();
        $items = [];

        if ($productId) {
            $product = $objectManager->create(\Magestore\Webpos\Model\Catalog\Product::class)
                ->load($productId);
            $barcodes = $product->getPosBarcode();
            if (in_array($searchCriteria->getQueryString(), explode(',', $barcodes))) {
                $items = [$product];
            }
        }
        $searchResult->setItems($items);
        $searchResult->setTotalCount(count($items));
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function prepareCollection($searchCriteria, $barcode = false)
    {
        if (!empty($this->_productCollection)) {
            return;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $categoryFilter = null;

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'category_id') {
                    $categoryFilter = str_replace("%", "", $filter->getValue());
                }
            }
        }

        $useIndexedTable = $this->useIndexedTable() && $categoryFilter === null;
        /** @var \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection $collection */
        if ($useIndexedTable) {
            $collection = $objectManager
                ->create(\Magestore\Webpos\Model\ResourceModel\Catalog\Search\Collection::class);
            $collection->setItemObjectClass(\Magestore\Webpos\Model\Catalog\Product::class);
        } else {
            $collection = $objectManager
                ->create(\Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::class);
        }
        /** @var \Magestore\Webpos\Helper\Data $helper */
        $helper = $objectManager->get(\Magestore\Webpos\Helper\Data::class);
        $storeId = $helper->getCurrentStoreView()->getId();
        $collection->setStoreId($storeId);
        if (!$useIndexedTable) {
            $collection->addStoreFilter($storeId);
        }

        $this->_eventManagerInterFace = $objectManager->get(\Magento\Framework\Event\ManagerInterface::class);
        /** @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository */
        $sessionRepository = $objectManager->get(\Magestore\Webpos\Api\Staff\SessionRepositoryInterface::class);
        $this->request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        $session = $sessionRepository->getBySessionId(
            $this->request->getParam(
                \Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY
            )
        );
        $this->_eventManagerInterFace->dispatch(
            'webpos_catalog_product_getlist',
            ['collection' => $collection, 'is_new' => true, 'location' => $session->getLocationId()]
        );
        /** End integrate webpos **/

        // Fix inventory
        if ($useIndexedTable) {
            $collection->setOrder('name', 'ASC');
            $collection->addFieldToFilter([
                'webpos_visible'/*,
                'webpos_visible',*/
            ], [
                /*['is' => new \Zend_Db_Expr('NULL')],*/
                ['eq' => 1],
            ]);
        } else {
            $collection->addAttributeToSelect($this->listAttributes);
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner'
            );
            $collection->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            );
            $collection->addAttributeToSort('name', 'ASC');
            $collection->addVisibleFilter();

            if ($categoryFilter !== null) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository */
                $categoryRepository = $objectManager->create(
                    \Magento\Catalog\Api\CategoryRepositoryInterface::class
                );
                try {
                    $category = $categoryRepository->get($categoryFilter);
                    $collection->addCategoryFilter($category);
                } catch (\Exception $e) {
                    $categoryFilter = [['in' => [$categoryFilter]]];
                    $collection->addCategoriesFilter($categoryFilter);
                }
            }
        }

        /** @var \Magestore\Webpos\Helper\Data $webposHelper */
        $webposHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magestore\Webpos\Helper\Data::class);
        $productTypeIds = $webposHelper->getProductTypeIds();
        $collection->addFieldToFilter('type_id', ['in' => $productTypeIds]);

        // Search data
        if ($barcode) {
            $queryString = $searchCriteria->getQueryString();
        } else {
            $queryString = '%' . $searchCriteria->getQueryString() . '%';
        }

        /** Integrate Inventory Barcode **/
        $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Event\ManagerInterface::class
        );
        $array = [];
        $result = new \Magento\Framework\DataObject();
        $result->setData($array);
        $eventManage->dispatch(
            'webpos_catalog_product_search_online',
            ['search_string' => $queryString, 'result' => $result]
        );

        if ($barcode) {
            $this->scopeConfig = $objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
            $barcodeAttr = $this->scopeConfig->getValue('webpos/product_search/barcode');
            if ($useIndexedTable) {
                if ($barcodeAttr === 'sku') {
                    $barcodeAttr = 'e.sku';
                }
                $attributes = [$barcodeAttr];
                $conditions = [['eq' => $queryString]];
                if ($result->getData() && !empty($result->getData())) {
                    $attributes[] = 'e.sku';
                    $conditions[] = ['in' => $result->getData()];
                }
                $collection->addFieldToFilter($attributes, $conditions);
            } else {
                $attrConditions[] = [
                    'attribute' => $barcodeAttr,
                    'like' => $queryString,
                ];
                if ($result->getData() && !empty($result->getData())) {
                    $attrConditions[] = [
                        'attribute' => 'sku',
                        'in' => $result->getData(),
                    ];
                }
                $collection->addAttributeToFilter($attrConditions);
            }
        } else {
            $searchAttrs = $this->getSearchAttributes();
            $conditions = [];
            if ($useIndexedTable) {
                foreach ($searchAttrs as &$attribute) {
                    if ($attribute === 'sku') {
                        $attribute = 'e.sku';
                    }
                    $conditions[] = [
                        'like' => $queryString,
                    ];
                }
                if ($result->getData() && !empty($result->getData())) {
                    $searchAttrs[] = 'e.sku';
                    $conditions[] = ['in' => $result->getData()];
                }
                $collection->addFieldToFilter($searchAttrs, $conditions);
            } else {
                foreach ($searchAttrs as $attribute) {
                    $conditions[] = [
                        'attribute' => $attribute,
                        'like' => $queryString,
                    ];
                }
                if ($result->getData() && !empty($result->getData())) {
                    $conditions[] = [
                        'attribute' => 'sku',
                        'in' => $result->getData()
                    ];
                }
                $collection->addAttributeToFilter($conditions);
            }
        }

        $this->filterProductByStockAndSource($collection);

        $this->_productCollection = $collection;
    }
}
