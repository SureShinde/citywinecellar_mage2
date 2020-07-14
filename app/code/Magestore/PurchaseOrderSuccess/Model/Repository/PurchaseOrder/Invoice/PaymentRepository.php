<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice;

use \Magestore\PurchaseOrderSuccess\Api\PaymentRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\PaymentSearchResultsInterfaceFactory;

/**
 * Class PaymentRepository
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Invoice
 */
class PaymentRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements PaymentRepositoryInterface
{

    /**
     * Item constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\PaymentFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Payment $resourceModel
     * @param PaymentSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Invoice\PaymentFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Payment $resourceModel,
        PaymentSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order invoice payment that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PaymentSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a purchase order invoice payment by id
     *
     * @param int $id purchase order invoice payment id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create purchase order invoice payment
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $payment
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $payment){
        return $this->processSave($payment);
    }
    
    /**
     * Deletes a specified purchase order payment.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $invoice
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\PaymentInterface $payment){
        return $this->processDelete($payment);
    }

    /**
     * Deletes a specified purchase order invoice payment by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id){
        return $this->processDeleteById($id);
    }

    /**
     * Return message for could not found exception
     *
     * @return \Magento\Framework\Phrase
     */
    public function notFoundMessage(){
        return __('This payment was not created.');
    }

    /**
     * Return message for could not delete exception
     *
     * @return \Magento\Framework\Phrase
     */
    public function couldNotDeleteMessage(){
        return __('Could not delete this payment.');
    }
}