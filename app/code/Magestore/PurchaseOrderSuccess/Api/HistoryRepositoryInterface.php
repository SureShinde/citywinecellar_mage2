<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface HistoryRepositoryInterface
{
    /**
     * Get list purchase order history that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\HistorySearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order history by id.
     *
     * @param int $id purchase order history id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order history
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface $history
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface $history);
}