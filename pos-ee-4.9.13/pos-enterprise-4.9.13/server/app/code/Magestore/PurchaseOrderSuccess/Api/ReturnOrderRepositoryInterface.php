<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface ReturnOrderRepositoryInterface
{
    /**
     * Get list return order that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get list return order of a supplier;
     *
     * @param int $supplierId
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderSearchResultsInterface
     */
    public function getListBySupplierId($supplierId);

    /**
     * Get list return order of a warehouse;
     *
     * @param int $warehouseId
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderSearchResultsInterface
     */
    public function getListByWarehouseId($warehouseId);

    /**
     * Get a return order by id.
     *
     * @param int $id return order id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);

    /**
     * Create return order
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder);

    /**
     * Deletes a specified return order.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder);

    /**
     * Deletes a specified return order by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Cancel a specified return order by id.
     *
     * @param int $id
     * @return bool
     */
    public function cancel($id);

    /**
     * Confirm a specified return order by id.
     *
     * @param int $id
     * @return bool
     */
    public function confirm($id);

    /**
     * Complete a specified return order by id.
     *
     * @param int $id
     * @return bool
     */
    public function complete($id);

}