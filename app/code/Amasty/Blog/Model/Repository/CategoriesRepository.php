<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Repository;

use Amasty\Blog\Api\CategoryRepositoryInterface;
use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Block\Sidebar\Category\TreeRenderer;
use Amasty\Blog\Model\CategoriesFactory;
use Amasty\Blog\Model\ResourceModel\Categories as CategoriesResource;
use Amasty\Blog\Model\ResourceModel\Categories\Collection;
use Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory;
use Amasty\Blog\Model\Source\CategoryStatus;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class CategoriesRepository implements CategoryRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CategoriesFactory
     */
    private $categoriesFactory;

    /**
     * @var CategoriesResource
     */
    private $categoriesResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $categoriess;

    /**
     * @var CollectionFactory
     */
    private $categoriesCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Blog\Helper\Settings
     */
    private $settings;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        CategoriesFactory $categoriesFactory,
        CategoriesResource $categoriesResource,
        CollectionFactory $categoriesCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Blog\Helper\Settings $settings
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->categoriesFactory = $categoriesFactory;
        $this->categoriesResource = $categoriesResource;
        $this->categoriesCollectionFactory = $categoriesCollectionFactory;
        $this->storeManager = $storeManager;
        $this->settings = $settings;
    }

    /**
     * @param CategoryInterface $categories
     *
     * @return CategoryInterface
     * @throws CouldNotSaveException
     */
    public function save(CategoryInterface $categories)
    {
        try {
            if ($categories->getCategoryId()) {
                $categories = $this->getById($categories->getCategoryId())->addData($categories->getData());
            } else {
                $categories->setCategoryId(null);
            }
            $this->categoriesResource->save($categories);
            unset($this->categoriess[$categories->getCategoryId()]);
        } catch (\Exception $e) {
            if ($categories->getCategoryId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save categories with ID %1. Error: %2',
                        [$categories->getCategoryId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new categories. Error: %1', $e->getMessage()));
        }

        return $categories;
    }

    /**
     * @param int $categoryId
     *
     * @return \Amasty\Blog\Model\Categories
     * @throws NoSuchEntityException
     */
    public function getById($categoryId)
    {
        if (!isset($this->categoriess[$categoryId])) {
            /** @var \Amasty\Blog\Model\Categories $categories */
            $categories = $this->categoriesFactory->create();
            $this->categoriesResource->load($categories, $categoryId);

            if (!$categories->getCategoryId()) {
                throw new NoSuchEntityException(__('Categories with specified ID "%1" not found.', $categoryId));
            }

            $this->categoriess[$categoryId] = $categories;
        }

        return $this->categoriess[$categoryId];
    }

    /**
     * @param string $urlKey
     * @return \Amasty\Blog\Model\Categories|\Magento\Framework\DataObject
     * @throws NoSuchEntityException
     */
    public function getByUrlKey($urlKey)
    {
        $collection = $this->getActiveCategories();
        $collection->addFieldToFilter('url_key', $urlKey);
        $collection->setLimit(1);

        return $collection->getFirstItem();
    }

    /**
     * @param CategoryInterface $categories
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CategoryInterface $categories)
    {
        try {
            $this->categoriesResource->delete($categories);
            unset($this->categoriess[$categories->getCategoryId()]);
        } catch (\Exception $e) {
            if ($categories->getCategoryId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove categories with ID %1. Error: %2',
                        [$categories->getCategoryId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove categories. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $categoryId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($categoryId)
    {
        $categoriesModel = $this->getById($categoryId);
        $this->delete($categoriesModel);

        return true;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface|\Magento\Ui\Api\Data\BookmarkSearchResultsInterface
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Blog\Model\ResourceModel\Categories\Collection $categoriesCollection */
        $categoriesCollection = $this->categoriesCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $categoriesCollection);
        }

        $searchResults->setTotalCount($categoriesCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $categoriesCollection);
        }

        $categoriesCollection->setCurPage($searchCriteria->getCurrentPage());
        $categoriesCollection->setPageSize($searchCriteria->getPageSize());

        $categories = [];
        /** @var CategoryInterface $categories */
        foreach ($categoriesCollection->getItems() as $categoryItem) {
            $categories[] = $this->getById($categoryItem->getCategoryId());
        }

        $searchResults->setItems($categories);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $categoriesCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $categoriesCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $categoriesCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection $categoriesCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $categoriesCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $categoriesCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getAllCategories()
    {
        return $this->categoriesCollectionFactory->create()->addDefaultStore()->getItems();
    }

    /**
     * @return \Amasty\Blog\Model\Categories
     */
    public function getCategory()
    {
        return $this->categoriesFactory->create();
    }

    /**
     * @param $categoryId
     *
     * @return array
     */
    public function getStores($categoryId)
    {
        return $this->categoriesResource->getStores($categoryId);
    }

    /**
     * @param $postId
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCategoriesByPost($postId)
    {
        return $this->getActiveCategories()->addPostFilter($postId);
    }

    /**
     * @param array $categoryIds
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCategoriesByIds($categoryIds = [])
    {
        return $this->getActiveCategories()->addIdFilter($categoryIds);
    }

    /**
     * @param null $storeId
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getActiveCategories($storeId = null)
    {
        $categories = $this->categoriesCollectionFactory->create();
        $categories->addStatusFilter(CategoryStatus::STATUS_ENABLED);
        $storeId = $storeId === null ? $this->storeManager->getStore()->getId() : $storeId;
        $categories->addStoreWithDefault($storeId);

        return $categories;
    }

    /**
     * @param $parentId
     * @param $limit
     * @param int $storeId
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getChildrenCategories($parentId, $limit = 0, $storeId = null)
    {
        $collection = $this->getActiveCategories($storeId);
        $collection->addFieldToFilter(CategoryInterface::PARENT_ID, $parentId);
        $collection->setPageSize($limit ?: $this->settings->getCategoriesLimit());
        $collection->getSelect()->where('main_table.level <= ?', TreeRenderer::LEVEL_LIMIT);
        $collection->setSortOrder('asc');

        return $collection;
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     * @param bool $isAddDefaultStore
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByIdAndStore($categoryId, $storeId = 0, $isAddDefaultStore = true)
    {
        $collection = $this->categoriesCollectionFactory->create();
        if ($isAddDefaultStore) {
            $collection->addStoreWithDefault($storeId);
        } else {
            $collection->addStore($storeId);
        }

        $collection->addFieldToFilter(CategoryInterface::CATEGORY_ID, $categoryId);
        $collection->setLimit(1);

        return $collection->getFirstItem();
    }
}
