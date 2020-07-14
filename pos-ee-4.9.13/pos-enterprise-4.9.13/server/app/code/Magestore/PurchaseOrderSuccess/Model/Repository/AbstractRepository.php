<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository;

use \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderSearchResultsInterface;

class AbstractRepository
{
    protected $modelFactory;

    protected $resouceModel;

    protected $searchResultFactory;

    /**
     * Get list model that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function processGetList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        $searchResult = $this->searchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setCurPage($searchCriteria->getCurrentPage());
        $searchResult->setPageSize($searchCriteria->getPageSize());
        return $searchResult;
    }

    /**
     * Get a model by id
     *
     * @param int $id
     * @return $model
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function processGet($id){
        $model = $this->modelFactory->create();
        $this->resouceModel->load($model, $id);
        if(!$model->getId()){
            throw new \Magento\Framework\Exception\NotFoundException(
                $this->notFoundMessage()
            );
        }
        return $model;
    }

    /**
     * Create purchase order
     *
     * @param $model
     * @return $model
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function processSave($model){
        $this->resouceModel->save($model);
        return $model;
    }

    /**
     * Deletes a specified purchase order.
     *
     * @param $model
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function processDelete($model){
        try{
            $this->resouceModel->delete($model);
        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                $this->couldNotDeleteMessage()
            );
        }
        return true;
    }

    /**
     * Deletes a record by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function processDeleteById($id){
        $model = $this->get($id);
        return $this->delete($model);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\SearchResultsInterface $searchResult
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\SearchResultsInterface $searchResult
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }
        if ($fields) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Return message for could not found exception
     * 
     * @return \Magento\Framework\Phrase
     */
    public function notFoundMessage(){
        return __('This item was not created.');
    }

    /**
     * Return message for could not delete exception
     * 
     * @return \Magento\Framework\Phrase
     */
    public function couldNotDeleteMessage(){
        return __('Could not delete this item.');
    }
}