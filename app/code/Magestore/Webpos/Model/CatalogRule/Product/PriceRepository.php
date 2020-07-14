<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\CatalogRule\Product;

/**
 * Customer repository.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PriceRepository implements \Magestore\Webpos\Api\CatalogRule\Product\PriceRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceIdsSearchResultsInterfaceFactory
     */
    protected $searchIdsResultsFactory;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * PriceRepository constructor.
     * @param \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceIdsSearchResultsInterfaceFactory $searchIdsResultsFactory
     * @param \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\CollectionFactory $collectionFactory
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceSearchResultsInterfaceFactory $searchResultsFactory,
        \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceIdsSearchResultsInterfaceFactory $searchIdsResultsFactory,
        \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\CollectionFactory $collectionFactory,
        \Magestore\Webpos\Helper\Data $helper
    )
    {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchIdsResultsFactory = $searchIdsResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
    }

    /**
     * Retrieve Catalog rule  product price which match a specified criteria.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        /** @var \Magento\Framework\App\Cache\StateInterface $cacheState */
        $cacheState = $objectManager->get('Magento\Framework\App\Cache\StateInterface');
        if (!$cacheState->isEnabled(\Magestore\Webpos\Model\Cache\Type::TYPE_IDENTIFIER)
            || count($searchCriteria->getFilterGroups())
        ) {
            return $this->processSync($searchCriteria);
        }
        
        /** @var \Magento\Framework\App\CacheInterface $cache */
        $cache = $objectManager->get('Magento\Framework\App\CacheInterface');
        
        $key = 'syncCatalogRules-'
            . $searchCriteria->getPageSize()
            . '-' . $searchCriteria->getCurrentPage()
            . '-' . $this->helper->getCurrentStoreView()->getWebsiteId();
        
        if ($response = $cache->load($key)) {
            // Reponse from cache
            return json_decode($response, true);
        }
        
        $flag = \Magestore\Webpos\Api\SyncInterface::QUEUE_NAME;
        if ($cachedAt = $cache->load($flag)) {
            return [
                'async_stage'   => 2, // processing
                'cached_at'     => $cachedAt, // process started time
            ];
        }
        
        // Block metaphor
        $cachedAt = date("Y-m-d H:i:s");
        $cache->save($cachedAt, $flag, [\Magestore\Webpos\Model\Cache\Type::CACHE_TAG], 300);
        
        /** @var \Magento\Framework\Webapi\ServiceOutputProcessor $processor */
        $processor = $objectManager->get('Magento\Framework\Webapi\ServiceOutputProcessor');
        $outputData = $processor->process(
            $this->processSync($searchCriteria),
            'Magestore\Webpos\Api\CatalogRule\Product\PriceRepositoryInterface',
            'sync'
        );
        $outputData['cached_at'] = $cachedAt;
        $cache->save(
            json_encode($outputData),
            $key,
            [
                \Magestore\Webpos\Model\Cache\Type::CACHE_TAG,
            ],
            43200   // Cache lifetime: 0.5 day
        );
        // Release metaphor
        $cache->remove($flag);
        return $outputData;
    }
    
    /**
     * @see \Magestore\Webpos\Api\CatalogRule\Product\PriceRepositoryInterface::sync()
     */
    public function processSync(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $store = $this->helper->getCurrentStoreView();
        $websiteId = $store->getWebsiteId();

        $isSync = true;

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface::UPDATED_TIME) {
                    $isSync = false;
                }
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $searchResults->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->addFieldToFilter('website_id', ['eq' => $websiteId]);

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $searchResults->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else if(!$isSync) {
            $searchResults->addOrder(
                \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceInterface::UPDATED_TIME,
                'ASC'
            );
        }

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setCurPage($searchCriteria->getCurrentPage());
        $searchResults->setPageSize($searchCriteria->getPageSize());
        /*if(!$isSync) {
            $ruleProductPriceIds = $this->getRuleProductPriceIds();
            $searchResults->setRuleProductPriceIds($ruleProductPriceIds);
        }*/
        return $searchResults;
    }

    public function getRuleProductPriceIds()
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\Collection $collection */
        $collection = $this->collectionFactory->create();
        $select = $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(['rule_product_price_id']);
        return $collection->getResource()->getConnection()->fetchCol($select);
    }
    /**
     * Retrieve Catalog rule product price ids which match a specified criteria.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceIdsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllIds(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Data\CatalogRule\Product\PriceIdsSearchResultsInterface $searchResults */
        $searchResults = $this->searchIdsResultsFactory->create();
        $store = $this->helper->getCurrentStoreView();
        $websiteId = $store->getWebsiteId();

        /** @var \Magestore\Webpos\Model\ResourceModel\CatalogRule\Product\Price\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->addFieldToFilter('website_id', ['eq' => $websiteId]);
        $select = $collection->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->order('rule_product_price_id ASC')
            ->columns(['rule_product_price_id']);

        if($searchCriteria->getPageSize() && $searchCriteria->getCurrentPage()) {
            $select->limit($searchCriteria->getPageSize(), $searchCriteria->getPageSize() * ($searchCriteria->getCurrentPage() - 1));
        }

        $resouce = $collection->getResource()->getConnection();
        $items = $resouce->fetchCol($select);

        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->columns(new \Zend_Db_Expr(("COUNT(DISTINCT rule_product_price_id)")));
        
        $totalCount = $resouce->fetchOne($select);
        
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($items);
        $searchResults->setTotalCount($totalCount);
        return $searchResults;
    }
}
