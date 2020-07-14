<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\ReturnOrder\Item;

use \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemTransferredRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredSearchResultsInterfaceFactory;

/**
 * Class TransferredRepository
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder\Item
 */
class TransferredRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements ReturnOrderItemTransferredRepositoryInterface
{
    protected $modelFactory;

    protected $resouceModel;

    protected $searchResultFactory;

    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item\TransferredFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred $resourceModel,
        ReturnOrderItemTransferredSearchResultsInterfaceFactory $searchResultFactory
    )
    {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Get list return order item transferred that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a return order item transferred by id
     *
     * @param int $id return order transferred id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create return order item transferred
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface $returnOrderItemTransferred
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface $returnOrderItemTransferred){
        return $this->processSave($returnOrderItemTransferred);
    }
}