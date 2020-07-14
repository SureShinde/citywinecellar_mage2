<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface ReturnOrderItemRepositoryInterface
{
    /**
     * Get list return order item that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get a return order item by id
     *
     * @param int $id return order item id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);

    /**
     * Create return order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface $returnOrderItem
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface $returnOrderItem);

    /**
     * Deletes a specified return order item.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface $returnOrderItem
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface $returnOrderItem);

    /**
     * Deletes a specified return order item by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Add products to return order from products data
     *
     * @param array $returnProductsData
     * @return boolean
     */
    public function addProductsToReturnOrder($returnProductsData = []);
}