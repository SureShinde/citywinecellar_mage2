<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CategoryRepository extends \Magento\Catalog\Model\CategoryRepository
    implements \Magestore\Webpos\Api\Catalog\CategoryRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tree
     */
    protected $treeCategory;

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Block\Adminhtml\Category\Tree $treeCategory,
        \Magestore\Webpos\Helper\Data $helper
    )
    {
        parent::__construct($categoryFactory, $categoryResource, $storeManager);
        $this->treeCategory = $treeCategory;
        $this->helper = $helper;
    }

    /**
     * Get category list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Catalog\CategorySearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $store = $this->helper->getCurrentStoreView();
        $storeId = $store->getId();
        $rootCategory = $store->getRootCategoryId();
        $collection = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magestore\Webpos\Model\ResourceModel\Catalog\Category\Collection'
        );
        $isShowFirstCats = false;
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $fields = [];
            foreach ($group->getFilters() as $filter) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getField() == 'first_category') {
                    $isShowFirstCats = true;
                    continue;
                }
                $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields);
            }
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $direction = ($sortOrder->getDirection() == 'ASC') ? 'ASC' : 'DESC';
            $collection->addAttributeToSort($field, $direction);
        }
        if ($isShowFirstCats) {
            $collection->addFieldToFilter('parent_id', $rootCategory);
        }
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToSelect('path');
        $collection->addAttributeToSelect('parent_id');
        $collection->addAttributeToSelect('is_active');
        $collection->addAttributeToFilter(\Magento\Catalog\Model\Category::KEY_IS_ACTIVE, '1');
        $collection->setStoreId($storeId);
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();
        $searchResult = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magestore\Webpos\Api\Data\Catalog\CategorySearchResultsInterface'
        );
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
