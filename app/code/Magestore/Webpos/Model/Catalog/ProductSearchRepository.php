<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog;

use Magestore\Webpos\Helper\Profiler;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductSearchRepository extends ProductRepository
    implements \Magestore\Webpos\Api\Catalog\ProductSearchRepositoryInterface
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
        $this->scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $barcodeAttr = $this->scopeConfig->getValue('webpos/product_search/barcode');

        if ($barcodeAttr && !in_array($barcodeAttr, $searchAttrs)) {
            $searchAttrs[] = $barcodeAttr;
        }

        return $searchAttrs;
    }

    /**
     * {@inheritdoc}
     */
    public function barcode(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->search($searchCriteria, true);
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria, $barcode = false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->registry = $objectManager->get('Magento\Framework\Registry');
        $this->registry->register('webpos_get_product_list', true);
        $this->request = $objectManager->get('Magento\Framework\App\RequestInterface');
        // Show stock by default
        $this->registry->register('wp_is_show_stock', true);
        $this->registry->register('wp_is_show_options', (boolean)$this->request->getParam('show_option'));
        $this->registry->register('wp_is_search_product', true);

        Profiler::start('search');

        $this->prepareCollection($searchCriteria, $barcode);
        $this->_productCollection->setCurPage($searchCriteria->getCurrentPage());
        $this->_productCollection->setPageSize($searchCriteria->getPageSize());
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        Profiler::start('load');
        $this->_productCollection->load();
        $totalCount = $this->_productCollection->getSize();
        Profiler::stop('load');

        Profiler::start('items');
        $items = $this->_productCollection->getItems();
        if ($barcode) {
            foreach ($items as $product) {
                Profiler::start('get_product_' . $product->getEntityId());
                $product->getProduct();
                Profiler::stop('get_product_' . $product->getEntityId());
            }
        } else if (count($items)
            && $this->_productCollection instanceof \Magestore\Webpos\Model\ResourceModel\Catalog\Search\Collection
        ) {
            // Reinit collection from base data
            $collection = $objectManager->create('Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection');
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

    /**
     * Check can use indexed table for searching
     *
     * @return boolean
     */
    public function useIndexedTable()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magestore\Webpos\Model\Indexer\Product\Processor $processor */
        $processor = $objectManager->get('Magestore\Webpos\Model\Indexer\Product\Processor');
        return $processor->getIndexer()->isValid();
    }

    /**
     * {@inheritdoc}
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
            $collection = $objectManager->create('Magestore\Webpos\Model\ResourceModel\Catalog\Search\Collection');
            $collection->setItemObjectClass('Magestore\Webpos\Model\Catalog\Product');
        } else {
            $collection = $objectManager->create('Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection');
        }
        /** @var \Magestore\Webpos\Helper\Data $helper */
        $helper = $objectManager->get('Magestore\Webpos\Helper\Data');
        $storeId = $helper->getCurrentStoreView()->getId();
        $collection->setStoreId($storeId);
        if (!$useIndexedTable) {
            $collection->addStoreFilter($storeId);
        }

        $this->_eventManagerInterFace = $objectManager->get('Magento\Framework\Event\ManagerInterface');
        /** @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository */
        $sessionRepository = $objectManager->get('\Magestore\Webpos\Api\Staff\SessionRepositoryInterface');
        $this->request = $objectManager->get('Magento\Framework\App\RequestInterface');
        $session = $sessionRepository->getBySessionId($this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY));
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
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $collection->addAttributeToSort('name', 'ASC');
            $collection->addVisibleFilter();

            if ($categoryFilter !== null) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository */
                $categoryRepository = $objectManager->create('Magento\Catalog\Api\CategoryRepositoryInterface');
                try {
                    $category = $categoryRepository->get($categoryFilter);
                    $collection->addCategoryFilter($category);
                } catch (\Exception $e) {
                    $categoryFilter = [['in' => [$categoryFilter]]];
                    $collection->addCategoriesFilter($categoryFilter);
                }
            }
        }

        $collection->addFieldToFilter('type_id', ['in' => $this->getProductTypeIds()]);


        // Search data
        if ($barcode) {
            $queryString = $searchCriteria->getQueryString();
        } else {
            $queryString = '%' . $searchCriteria->getQueryString() . '%';
        }

        /** Integrate Inventory Barcode **/
        $eventManage = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Framework\Event\ManagerInterface'
        );
        $array = [];
        $result = new \Magento\Framework\DataObject();
        $result->setData($array);
        $eventManage->dispatch(
            'webpos_catalog_product_search_online',
            ['search_string' => $queryString, 'result' => $result]
        );

        if ($barcode) {
            $this->scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
            $barcodeAttr = $this->scopeConfig->getValue('webpos/product_search/barcode');
            // $collection->addFieldToFilter($barcodeAttr, $searchCriteria->getQueryString());
            if ($useIndexedTable) {
                if($barcodeAttr === 'sku') {
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
