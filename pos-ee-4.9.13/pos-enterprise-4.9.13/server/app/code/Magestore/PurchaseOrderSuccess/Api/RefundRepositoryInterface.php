<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface RefundRepositoryInterface
{
    /**
     * Get list purchase order invoice refund that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\RefundSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order invoice refund by id.
     *
     * @param int $id purchase order invoice refund id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order invoice refund
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund);
    
    /**
     * Deletes a specified purchase order invoice refund.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\RefundInterface $refund);    
    
    /**
     * Deletes a specified purchase order invoice refund by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}