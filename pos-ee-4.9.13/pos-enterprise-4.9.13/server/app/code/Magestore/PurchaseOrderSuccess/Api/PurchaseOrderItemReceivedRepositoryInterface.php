<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface PurchaseOrderItemReceivedRepositoryInterface
{
    /**
     * Get list purchase order item received that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReceivedSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order item received by id
     *
     * @param int $id purchase order received id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReceivedInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order item received
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReceivedInterface $purchaseOrderItemReceived
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReceivedInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReceivedInterface $purchaseOrderItemReceived);
}