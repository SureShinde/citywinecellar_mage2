<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface PurchaseOrderItemTransferredRepositoryInterface
{
    /**
     * Get list purchase order item transferred that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order item transferred by id
     *
     * @param int $id purchase order transferred id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order item transferred
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface $purchaseOrderItemTransferred
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface $purchaseOrderItemTransferred);
}