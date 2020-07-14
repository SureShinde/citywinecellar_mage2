<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;
/**
 * Class DataLogRepository
 * @package Magestore\Webpos\Model\Log
 */
class DataLogRepository implements \Magestore\Webpos\Api\Log\DataLogRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted\CollectionFactory
     */
    protected $productDeletedCollection;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted\CollectionFactory
     */
    protected $customerDeletedCollection;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory
     */
    protected $categoryDeletedCollection;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted\CollectionFactory
     */
    protected $orderDeletedCollection;

    /**
     * @var \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
     */
    protected $dataLogResults;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * DataLogRepository constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted\CollectionFactory $productDeletedCollection
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted\CollectionFactory $customerDeletedCollection
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory $categoryDeletedCollection
     * @param \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted\CollectionFactory $orderDeletedCollection
     * @param \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface $dataLogResults
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Log\ProductDeleted\CollectionFactory $productDeletedCollection,
        \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted\CollectionFactory $customerDeletedCollection,
        \Magestore\Webpos\Model\ResourceModel\Log\CategoryDeleted\CollectionFactory $categoryDeletedCollection,
        \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted\CollectionFactory $orderDeletedCollection,
        \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface $dataLogResults,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
    )
    {
        $this->customerDeletedCollection = $customerDeletedCollection;
        $this->categoryDeletedCollection = $categoryDeletedCollection;
        $this->productDeletedCollection = $productDeletedCollection;
        $this->orderDeletedCollection = $orderDeletedCollection;
        $this->dataLogResults = $dataLogResults;
        $this->stockManagement = $stockManagement;
    }

    /**
     * Retrieve data matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListCustomer(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $customerDeletedCollection = $this->customerDeletedCollection->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getField() == 'updated_at') {
                    $customerDeletedCollection->addFieldToFilter('deleted_at', [$condition => $filter->getValue()]);
                }
            }
        }

        $customerDeletedIds = array();
        foreach ($customerDeletedCollection as $customer) {
            $customerDeletedIds[] = (int)$customer->getCustomerId();
        }
        $this->dataLogResults->setIds($customerDeletedIds);
        return $this->dataLogResults;
    }

    /**
     * @inheritdoc
     */
    public function getListCategory(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        $categoryDeletedCollection = $this->categoryDeletedCollection->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getField() == 'updated_at'){
                    $categoryDeletedCollection->addFieldToFilter('deleted_at', [$condition => $filter->getValue()]);
                } elseif ($filter->getField() == 'root_category_id') {
                    $categoryDeletedCollection->addFieldToFilter('root_category_id', [$condition => $filter->getValue()]);
                }
            }
        }

        $categoryDeletedIds = array();
        foreach ($categoryDeletedCollection as $category){
            $categoryDeletedIds[] = (int)$category->getCategoryId();
        }
        $this->dataLogResults->setIds($categoryDeletedIds);
        return $this->dataLogResults;
    }

    /**
     * Retrieve data matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListProduct(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $productDeletedCollection = $this->productDeletedCollection->create();
        $this->filterByStockId($productDeletedCollection);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getField() == 'updated_at') {
                    $productDeletedCollection->addFieldToFilter('deleted_at', [$condition => $filter->getValue()]);
                }
            }
        }

        $productDeletedIds = array();
        foreach ($productDeletedCollection as $product) {
            $productDeletedIds[] = (int)$product->getProductId();
        }
        $this->dataLogResults->setIds($productDeletedIds);
        return $this->dataLogResults;
    }

    /**
     * Retrieve data matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Log\DataLogResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListOrder(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $orderDeletedCollection = $this->orderDeletedCollection->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                if ($filter->getField() == 'updated_at') {
                    $orderDeletedCollection->addFieldToFilter('deleted_at', [$condition => $filter->getValue()]);
                }
            }
        }

        $orderDeletedIds = array();
        foreach ($orderDeletedCollection as $order) {
            $orderDeletedIds[] = $order->getOrderIncrementId();
        }
        $this->dataLogResults->setIds($orderDeletedIds);
        return $this->dataLogResults;
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function filterByStockId(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection)
    {
        if ($stockId = $this->stockManagement->getStockId()) {
            $collection->addFieldToFilter('stock_id', $stockId);
        }
        return $collection;
    }
}
