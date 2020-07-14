<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder;

use \Magestore\PurchaseOrderSuccess\Api\InvoiceRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceSearchResultsInterfaceFactory;

/**
 * Class InvoiceRepository
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder
 */
class InvoiceRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements InvoiceRepositoryInterface
{

    /**
     * Item constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\InvoiceFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice $resourceModel
     * @param InvoiceSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\InvoiceFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice $resourceModel,
        InvoiceSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order invoice that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a purchase order invoice by id
     *
     * @param int $id purchase order invoice id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create purchase order invoice
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface $invoice
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface $invoice){
        return $this->processSave($invoice);
    }
    
    /**
     * Deletes a specified purchase order invoice.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface $invoice
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\InvoiceInterface $invoice){
        return $this->processDelete($invoice);
    }

    /**
     * Deletes a specified purchase order invoice by id.
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
        return __('This invoice was not created.');
    }

    /**
     * Return message for could not delete exception
     *
     * @return \Magento\Framework\Phrase
     */
    public function couldNotDeleteMessage(){
        return __('Could not delete this invoice.');
    }
}