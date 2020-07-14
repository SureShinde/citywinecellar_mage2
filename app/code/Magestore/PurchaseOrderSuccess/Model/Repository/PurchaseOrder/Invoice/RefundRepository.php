<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice;

use \Magestore\PurchaseOrderSuccess\Api\RefundRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\RefundSearchResultsInterfaceFactory;

/**
 * Class RefundRepository
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice
 */
class RefundRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements RefundRepositoryInterface
{

    /**
     * Item constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\RefundFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Refund $resourceModel
     * @param RefundSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\RefundFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Refund $resourceModel,
        RefundSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order invoice refund that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\RefundSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a purchase order invoice refund by id
     *
     * @param int $id purchase order invoice refund id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create purchase order invoice refund
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund){
        return $this->processSave($refund);
    }
    
    /**
     * Deletes a specified purchase order invoice refund.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund){
        return $this->processDelete($refund);
    }

    /**
     * Deletes a specified purchase order invoice refund by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id){
        return $this->processDeleteById($id);
    }

    /**
     * Return message for could not found exception
     *
     * @return \Magento\Framework\Phrase
     */
    public function notFoundMessage(){
        return __('This refund was not created.');
    }

    /**
     * Return message for could not delete exception
     *
     * @return \Magento\Framework\Phrase
     */
    public function couldNotDeleteMessage(){
        return __('Could not delete this refund.');
    }
}