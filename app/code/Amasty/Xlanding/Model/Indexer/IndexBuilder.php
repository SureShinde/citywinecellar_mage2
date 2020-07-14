<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Indexer;

use Amasty\Xlanding\Model\Page\Product\IndexDataProvider;
use Amasty\Xlanding\Model\ResourceModel\Page\Collection;
use Amasty\Xlanding\Model\ResourceModel\Page\CollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Amasty\Xlanding\Model\Page;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IndexBuilder
{
    const PRODUCT_ID = 'product_id';
    const TABLE_NAME = 'amasty_xlanding_page_product_index';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var IndexDataProvider
     */
    private $indexDataProvider;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var int
     */
    private $batchCount;

    /**
     * @var int
     */
    private $batchCacheCount;
    /**
     * @var EventManager
     */
    private $eventManager;
    /**
     * @var CacheContext
     */
    private $cacheContext;

    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        CollectionFactory $pageCollectionFactory,
        IndexDataProvider $indexDataProvider,
        State $appState,
        EventManager $eventManager,
        CacheContext $cacheContext,
        LoggerInterface $logger,
        $batchCount = 1000,
        $batchCacheCount = 100
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->indexDataProvider = $indexDataProvider;
        $this->logger = $logger;
        $this->appState = $appState;
        $this->batchCount = $batchCount;
        $this->batchCacheCount = $batchCacheCount;
        $this->eventManager = $eventManager;
        $this->cacheContext = $cacheContext;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function reindexFull()
    {
        if ($this->checkCorrectAreaCode()) {
            try {
                $this->resource->getConnection()->truncateTable($this->getIndexTable());
                $this->doReindex([], []);
            } catch (\Exception $e) {
                $this->critical($e);
                throw new LocalizedException(
                    __("Landing Page - Product indexing failed. See details in exception log.")
                );
            }
        }
    }

    /**
     * @param array $ids
     * @throws LocalizedException
     */
    public function reindexByPageIds(array $ids)
    {
        if ($this->checkCorrectAreaCode()) {
            $table = $this->getIndexTable();
            $this->resource->getConnection()->delete(
                $table,
                [$this->resource->getConnection()->quoteInto('page_id IN(?)', $ids)]
            );

            try {
                $this->doReindex($ids, []);
            } catch (\Exception $e) {
                $this->critical($e);
                throw new LocalizedException(
                    __("Landing Page - Product indexing failed. See details in exception log.")
                );
            }
        }
    }

    /**
     * @param array $ids
     * @throws LocalizedException
     */
    public function reindexByProductIds(array $ids)
    {
        if ($this->checkCorrectAreaCode()) {
            try {
                $this->cleanByProductIds($ids);
                $this->doReindex([], $ids);
            } catch (\Exception $e) {
                $this->critical($e);
                throw new LocalizedException(
                    __("Landing Page - Product rule indexing failed. See details in exception log.")
                );
            }
        }
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    private function checkCorrectAreaCode()
    {
        if ($this->appState->isAreaCodeEmulated()) {
            return $this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_FRONTEND;
        }
        return true;
    }

    /**
     * @param array $pageIds
     * @param array $productIds
     * @throws \Exception
     */
    private function doReindex(array $pageIds, array $productIds)
    {
        $rows = [];
        $size = 0;

        /** @var Page $page */
        foreach ($this->getPageCollection($pageIds) as $page) {
            foreach ($this->storeManager->getStores() as $store) {
                $pageProductPositions = $this->getProductPositionData($page, $store->getId(), $productIds);
                foreach ($pageProductPositions as $productId => $position) {
                    $rows[] = [
                        'page_id' => $page->getId(),
                        self::PRODUCT_ID => $productId,
                        'store_id' => $store->getId(),
                        'position' => $position
                    ];

                    $size++;
                    if ($size == $this->batchCount) {
                        $this->resource->getConnection()->insertOnDuplicate(
                            $this->getIndexTable(),
                            $rows,
                            ['position']
                        );
                        $rows = [];
                        $size = 0;
                    }
                }
            }
            $this->registerEntities(
                $page->getIsActive() === Page::STATUS_DYNAMIC ? Category::CACHE_TAG : Page::CACHE_TAG,
                [$page->getIsActive() === Page::STATUS_DYNAMIC ? $page->getDynamicCategoryId() : $page->getId()]
            );
        }

        $this->cleanCache();

        if (!empty($rows)) {
            $this->resource->getConnection()->insertOnDuplicate($this->getIndexTable(), $rows, ['position']);
        }
    }

    /**
     * @param $pageIds
     * @return Collection
     */
    private function getPageCollection($pageIds)
    {
        $collection = $this->pageCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['in' => [Page::STATUS_ACTIVE, Page::STATUS_DYNAMIC]]);
        if ($pageIds) {
            $collection->addFieldToFilter('page_id', ['in' => $pageIds]);
        }

        return $collection;
    }

    /**
     * @param Page $page
     * @param $storeId
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    private function getProductPositionData(Page $page, $storeId, $productIds = [])
    {
        try {
            $positionData = $this->indexDataProvider->getProductPositionData($page, $storeId, $productIds);
        } catch (\InvalidArgumentException $e) {
            $positionData = [];
        }

        return $positionData;
    }

    /**
     * @param array $productIds
     * @return void
     */
    private function cleanByProductIds($productIds)
    {
        $query = $this->resource->getConnection()->deleteFromSelect(
            $this->resource->getConnection()
                ->select()
                ->from($this->getIndexTable(), self::PRODUCT_ID)
                ->distinct()
                ->where(self::PRODUCT_ID . ' IN (?)', $productIds),
            $this->getIndexTable()
        );

        $this->resource->getConnection()->query($query);
    }

    /**
     * @return string
     */
    private function getIndexTable()
    {
        return $this->resource->getTableName(self::TABLE_NAME);
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    private function critical(\Exception $exception)
    {
        $this->logger->critical($exception);
    }

    /**
     * @param string $cacheTag
     * @param array $ids
     */
    private function registerEntities($cacheTag, $ids)
    {
        $this->cacheContext->registerEntities($cacheTag, $ids);
        if ($this->cacheContext->getSize() > $this->batchCacheCount) {
            $this->cleanCache();
            $this->cacheContext->flush();
        }
    }

    private function cleanCache()
    {
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
    }
}
