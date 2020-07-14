<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface PurchaseOrderItemReturnedRepositoryInterface
{
    /**
     * Get list purchase order item returned that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnedSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order item returned by id
     *
     * @param int $id purchase order returned id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnedInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order item returned
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnedInterface $purchaseOrderItemReturned
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnedInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnedInterface $purchaseOrderItemReturned);
}