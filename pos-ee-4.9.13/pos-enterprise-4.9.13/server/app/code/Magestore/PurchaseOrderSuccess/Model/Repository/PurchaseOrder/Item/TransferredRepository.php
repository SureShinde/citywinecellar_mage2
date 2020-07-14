<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Item;

use \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemTransferredRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredSearchResultsInterfaceFactory;

/**
 * Class TransferredRepository
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Item
 */
class TransferredRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements PurchaseOrderItemTransferredRepositoryInterface
{
    protected $modelFactory;
    
    protected $resouceModel;
    
    protected $searchResultFactory;
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\TransferredFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Transferred $resourceModel,
        PurchaseOrderItemTransferredSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order item transferred that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a purchase order item transferred by id
     *
     * @param int $id purchase order item id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create purchase order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface $purchaseOrderItemTransferred
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface $purchaseOrderItemTransferred){
        return $this->processSave($purchaseOrderItemTransferred);
    }

    /**
     * Return message for could not found exception
     *
     * @return \Magento\Framework\Phrase
     */
    public function notFoundMessage(){
        return __('This item was not created.');
    }
}