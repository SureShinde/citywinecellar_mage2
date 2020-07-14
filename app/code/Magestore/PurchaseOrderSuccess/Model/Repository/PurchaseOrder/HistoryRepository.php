<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder;

use \Magestore\PurchaseOrderSuccess\Api\HistoryRepositoryInterface;
use \Magestore\PurchaseOrderSuccess\Api\Data\HistorySearchResultsInterfaceFactory;

/**
 * Class HistoryRepository
 * @package Magestore\PurchaseOrderSuccess\Model\Repository\PurchaseOrder
 */
class HistoryRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements HistoryRepositoryInterface
{

    /**
     * Item constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\HistoryFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\History $resourceModel
     * @param HistorySearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\HistoryFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\History $resourceModel,
        HistorySearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }
    
    /**
     * Get list purchase order history that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\HistorySearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get a purchase order history by id
     *
     * @param int $id purchase order history id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id){
        return $this->processGet($id);
    }

    /**
     * Create purchase order history
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface $history
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface $history){
        return $this->processSave($history);
    }
}