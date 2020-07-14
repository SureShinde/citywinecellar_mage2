<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface InvoiceItemRepositoryInterface
{

    /**
     * Get list purchase order invoice item that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order invoice item by id.
     *
     * @param int $id purchase order item id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order invoice item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoiceItem
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoiceItem);
    
    /**
     * Deletes a specified purchase order invoice item.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoiceItem
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface $invoiceItem);    
    
    /**
     * Deletes a specified purchase order invoice item by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}