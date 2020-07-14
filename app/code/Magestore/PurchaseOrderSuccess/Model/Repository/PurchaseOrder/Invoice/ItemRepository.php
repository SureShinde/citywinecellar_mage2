<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice;

use \Magestore\PurchaseOrderSuccess\Api\InvoiceItemRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemSearchResultsInterfaceFactory;

/**
 * Class Item
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice
 */
class ItemRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements InvoiceItemRepositoryInterface
{

    /**
     * Item constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\ItemFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item $resourceModel
     * @param InvoiceItemSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\ItemFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item $resourceModel,
        InvoiceItemSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order invoice item that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a purchase order invoice item by id
     *
     * @param int $id purchase order invoice item id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create purchase order invoice item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoiceItem
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoiceItem){
        return $this->processSave($invoiceItem);
    }
    
    /**
     * Deletes a specified purchase order invoice.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoice
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoiceItem){
        return $this->processDelete($invoiceItem);
    }

    /**
     * Deletes a specified purchase order invoice item by id.
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
        return __('This invoice item was not created.');
    }

    /**
     * Return message for could not delete exception
     *
     * @return \Magento\Framework\Phrase
     */
    public function couldNotDeleteMessage(){
        return __('Could not delete this invoice item.');
    }
}