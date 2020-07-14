<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder;

use \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemSearchResultsInterfaceFactory;

/**
 * Class ItemRepository
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder
 */
class ItemRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements PurchaseOrderItemRepositoryInterface
{
    /**
     * Item constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\ItemFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item $resourceModel
     * @param PurchaseOrderItemSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\ItemFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item $resourceModel,
        PurchaseOrderItemSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order item that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a purchase order item by id
     *
     * @param int $id purchase order item id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create purchase order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem){
        return $this->processSave($purchaseOrderItem);
    }


    /**
     * Deletes a specified purchase order item.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem){
        $this->processDelete($purchaseOrderItem);
    }

    /**
     * Deletes a specified purchase order item by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id){
        $this->processDeleteById($id);
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

    /**
     * @param array $purchaseProductsData
     * @return $this|void
     */
    public function addProductsToPurchaseOrder($purchaseProductsData = []){
        return $this->resouceModel->addProductsToPurchaseOrder($purchaseProductsData);
    }
}