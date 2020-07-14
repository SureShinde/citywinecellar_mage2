<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface PurchaseOrderItemRepositoryInterface
{
    /**
     * Get list purchase order item that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order item by id
     *
     * @param int $id purchase order item id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem);
    
    /**
     * Deletes a specified purchase order item.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $purchaseOrderItem);

    /**
     * Deletes a specified purchase order item by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Add products to purchase order from products data
     * 
     * @param array $purchaseProductsData
     * @return boolean
     */
    public function addProductsToPurchaseOrder($purchaseProductsData = []);
}