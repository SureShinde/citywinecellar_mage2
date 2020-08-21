<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Catalog\Search;

use Magento\Search\Model\SearchEngine;
use Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface;
use Magestore\Webpos\Helper\Data as WebposHelper;
use Magestore\Webpos\Model\Search\Request\Builder;

/**
 * Class Collection
 *
 * Used for search product collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var Builder $builder
     */
    protected $builder;

    /**
     * @var \Magento\Search\Model\SearchEngine $searchEngine
     */
    protected $searchEngine;

    /**
     * @var \Magento\Framework\Search\SearchResponseBuilder
     */
    protected $searchResponseBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var WebposHelper
     */
    protected $webposHelper;
    /**
     * @var StockManagementInterface
     */
    protected $stockManagement;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Webpos\Model\Catalog\Search::class,
            \Magestore\Webpos\Model\ResourceModel\Catalog\Search::class
        );

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $this->builder = $objectManager->get(Builder::class);
        $this->searchResponseBuilder = $objectManager->get(\Magento\Framework\Search\SearchResponseBuilder::class);
        $this->searchEngine = $objectManager->get(SearchEngine::class);
        $this->webposHelper = $objectManager->get(WebposHelper::class);
        $this->stockManagement = $objectManager->get(StockManagementInterface::class);
    }
    
    /**
     * Set store ID for collection
     *
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->getResource()->setStoreId($storeId);
        $this->setMainTable($this->getResource()->getMainTable());
        $this->_reset();
    }
    
    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['e' => $this->getMainTable()]);
        return $this;
    }

    /**
     * Get SQL for get record count without left JOINs
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        if (!count($this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP))) {
            $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT ".implode(", ", $group).")")));
        $select = clone $countSelect;
        $countSelect->reset()->from($select, ['COUNT(*)']);
        return $countSelect;
    }

    /**
     * Search Webpos Product In search Engine
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function searchWebposProductInSearchEngine(
        \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $this->builder->bind('search_term', $searchCriteria->getQueryString());
        $this->builder->bind('allow_stock_id', $this->stockManagement->getStockId());
        $this->builder->bind('allow_product_type', $this->webposHelper->getProductTypeIds());
        $this->builder->setRequestName('webpos_productsearch_fulltext');
        $this->builder->bindDimension('scope', $this->storeManager->getStore()->getId());
        if ($searchCriteria->getCurrentPage()) {
            $this->builder->setFrom(($searchCriteria->getCurrentPage() - 1) * $searchCriteria->getPageSize());
        }
        if ($searchCriteria->getPageSize()) {
            $this->builder->setSize($searchCriteria->getPageSize());
        }

        $queryRequest = $this->builder->create();

        $queryResponse = $this->searchEngine->search($queryRequest);
        return $this->searchResponseBuilder->build($queryResponse)
            ->setSearchCriteria($searchCriteria);
    }

    /**
     * Get product Id by barcode
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\Search\SearchResult|\Magento\Framework\Api\Search\SearchResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductIdByBarcodeInElasticSearch(
        \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $this->builder->bind('allow_product_type', $this->webposHelper->getProductTypeIds());
        $this->builder->bind('allow_stock_id', $this->stockManagement->getStockId());
        $this->builder->bind('search_term', $searchCriteria->getQueryString());
        $this->builder->setRequestName('webpos_scan_barcode');
        $this->builder->bindDimension('scope', $this->storeManager->getStore()->getId());
        $queryRequest = $this->builder->create();

        $queryResponse = $this->searchEngine->search($queryRequest);
        return $this->searchResponseBuilder->build($queryResponse)
            ->setSearchCriteria($searchCriteria);
    }

    /**
     * Search Webpos Product In search Engine
     *
     * @param \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getProductListInSearchEngine(
        \Magestore\Webpos\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $this->builder->bind('allow_product_type', $this->webposHelper->getProductTypeIds());
        $this->builder->bind('allow_stock_id', $this->stockManagement->getStockId());
        $this->builder->setRequestName('webpos_product_list');
        $this->builder->bindDimension('scope', $this->storeManager->getStore()->getId());
        if ($searchCriteria->getCurrentPage()) {
            $this->builder->setFrom(($searchCriteria->getCurrentPage() - 1) * $searchCriteria->getPageSize());
        }
        if ($searchCriteria->getPageSize()) {
            $this->builder->setSize($searchCriteria->getPageSize());
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // Set sort order
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $objectManager->create(\Magento\Framework\Api\SortOrder::class);
        $sortOrder->setField($searchCriteria->getSortOrders()[0]->getField());
        $sortOrder->setDirection(
            $searchCriteria->getSortOrders()[0]->getDirection() ?: \Magento\Framework\Api\SortOrder::SORT_ASC
        );
        $this->builder->setSort([$sortOrder]);

        $queryRequest = $this->builder->create();

        $queryResponse = $this->searchEngine->search($queryRequest);
        return $this->searchResponseBuilder->build($queryResponse)
            ->setSearchCriteria($searchCriteria);
    }
}
