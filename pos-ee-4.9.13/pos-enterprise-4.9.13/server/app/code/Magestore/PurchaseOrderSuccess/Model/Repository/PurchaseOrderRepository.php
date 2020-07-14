<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository;

use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderSearchResultsInterfaceFactory;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory
     */
    protected $modelFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder
     */
    protected $resouceModel;

    /**
     * @var PurchaseOrderSearchResultsInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * PurchaseOrder constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder $resourceModel
     * @param PurchaseOrderSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder $resourceModel,
        PurchaseOrderSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
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
     * Get list purchase order of a supplier;
     *
     * @param int $supplierId
     * @param int|null $type
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemSearchResultsInterface
     */
    public function getListBySupplierId($supplierId, $type = null){
        $searchResult = $this->searchResultFactory->create()
            ->addFieldToFilter(PurchaseOrderInterface::SUPPLIER_ID, $supplierId);
        if($type)
            $searchResult->addFieldToFilter(PurchaseOrderInterface::TYPE, $type);
        return $searchResult;
    }

    /**
     * Get a purchase order by id.
     *
     * @param int $id purchase order id
     * @param string|null $typeLabel
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id, $typeLabel = null){
        $model = $this->modelFactory->create();
        $this->resouceModel->load($model, $id);
        if(!$model->getId()){
            $message = $this->getNotFoundExeptionMessage($typeLabel);
            throw new \Magento\Framework\Exception\NotFoundException($message);
        }
        return $model;
    }

    /**
     * Create purchase order
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder){
        $this->resouceModel->save($purchaseOrder);
        return $purchaseOrder;
    }

    /**
     * Deletes a specified purchase order.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder){
        try{
            $this->resouceModel->delete($purchaseOrder);
        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __('Could not delete purchase order.')
            );
        }
        return true;
    }

    /**
     * Deletes a specified purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id){
        $purchaseOrder = $this->get($id);
        return $this->delete($purchaseOrder);
    }

    /**
     * get purchase order by purchase key
     *
     * @param string $key
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getByKey($key) {
        $model = $this->modelFactory->create();
        $this->resouceModel->load($model, $key, PurchaseOrderInterface::PURCHASE_KEY);
        if(!$model->getId()){
            $message = $this->getNotFoundExeptionMessage('Not found PO by key!');
            throw new \Magento\Framework\Exception\NotFoundException($message);
        }
        return $model;
    }

    /**
     * Cancel a specified purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function cancel($id){
        try{
            $purchaseOrder = $this->get($id);
            if($purchaseOrder->getStatus() == Status::STATUS_COMPLETED)
                return false;
            $purchaseOrder->setStatus(Status::STATUS_CANCELED);
            $this->save($purchaseOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Convert a specified quotation to purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function convert($id){
        try{
            $purchaseOrder = $this->get($id);
            $purchaseOrder->setType(Type::TYPE_PURCHASE_ORDER);
            $purchaseOrder->setStatus(Status::STATUS_PENDING);
            $this->save($purchaseOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Confirm a specified purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function confirm($id){
        try{
            $purchaseOrder = $this->get($id);
            $purchaseOrder->setStatus(Status::STATUS_PROCESSING);
            $this->save($purchaseOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Confirm a specified quotation by id.
     *
     * @param int $id
     * @return bool
     */
    public function confirmQuotation($id){
        try{
            $purchaseOrder = $this->get($id);
            $purchaseOrder->setStatus(Status::STATUS_COMFIRMED);
            $this->save($purchaseOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Un-Confirm a specified purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function unConfirm($id){
        try{
            $purchaseOrder = $this->get($id);
            $purchaseOrder->setStatus(Status::STATUS_PENDING);
            $this->save($purchaseOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderSearchResultsInterface $searchResult
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderSearchResultsInterface $searchResult
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
     * Return not found purchase order message
     * 
     * @param null $typeLabel
     * @return \Magento\Framework\Phrase
     */
    public function getNotFoundExeptionMessage($typeLabel = null){
        $typeLabel = $typeLabel?$typeLabel:'Purchase Order';
        return __('%1 does not exist.', $typeLabel);
    }
}