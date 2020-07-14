<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface PurchaseOrderRepositoryInterface
{

    /**
     * Get list purchase order that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get list purchase order of a supplier;
     *
     * @param int $supplierId
     * @param int|null $type
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemSearchResultsInterface
     */
    public function getListBySupplierId($supplierId, $type = null);
    
    /**
     * Get a purchase order by id.
     *
     * @param int $id purchase order id
     * @param string|null $typeLabel
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id, $typeLabel = null);
    
    /**
     * Create purchase order
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder);
    
    /**
     * Deletes a specified purchase order.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder);    
    
    /**
     * Deletes a specified purchase order by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * get purchase order by purchase key
     *
     * @param string $key
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getByKey($key);

    /**
     * Cancel a specified purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function cancel($id);

    /**
     * Convert a specified quotation to purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function convert($id);

    /**
     * Confirm a specified purchase order by id.
     *
     * @param int $id
     * @return bool
     */
    public function confirm($id);

    /**
     * Confirm a specified quotation by id.
     *
     * @param int $id
     * @return bool
     */
    public function confirmQuotation($id);

    /**
     * Un-Confirm a specified quotation by id.
     *
     * @param int $id
     * @return bool
     */
    public function unConfirm($id);

}