<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface PaymentRepositoryInterface
{
    /**
     * Get list purchase order invoice payment that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PaymentSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Get a purchase order invoice payment by id.
     *
     * @param int $id purchase order invoice payment id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);
    
    /**
     * Create purchase order invoice payment
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $payment
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $payment);
    
    /**
     * Deletes a specified purchase order invoice payment.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $payment
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $payment);    
    
    /**
     * Deletes a specified purchase order invoice payment by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}