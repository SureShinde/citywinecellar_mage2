<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\Repository;

use Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface;
use Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status;

/**
 * @api
 */
class ReturnOrderRepository extends \Magestore\PurchaseOrderSuccess\Model\Repository\AbstractRepository
    implements ReturnOrderRepositoryInterface
{
    /**
     * ReturnOrderRepository constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\ReturnOrderFactory $modelFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder $resourceModel
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\ReturnOrderFactory $modelFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder $resourceModel,
        \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resouceModel = $resourceModel;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Get list return order that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria){
        return $this->processGetList($searchCriteria);
    }

    /**
     * Get list return order of a supplier;
     *
     * @param int $supplierId
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderSearchResultsInterface
     */
    public function getListBySupplierId($supplierId){
        $searchResult = $this->searchResultFactory->create()
            ->addFieldToFilter(ReturnOrderInterface::SUPPLIER_ID, $supplierId);
        return $searchResult;
    }

    /**
     * Get list return order of a warehouse;
     *
     * @param int $warehouseId
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderSearchResultsInterface
     */
    public function getListByWarehouseId($warehouseId){
        $searchResult = $this->searchResultFactory->create()
            ->addFieldToFilter(ReturnOrderInterface::WAREHOUSE_ID, $warehouseId);
        return $searchResult;
    }

    /**
     * Get a return order by id.
     *
     * @param int $id return order id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id) {
        return $this->processGet($id);
    }

    /**
     * Create return order
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder){
        return $this->processSave($returnOrder);
    }

    /**
     * Deletes a specified return order.
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder){
        $this->processDelete($returnOrder);
    }

    /**
     * Deletes a specified return order by id.
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id){
        $this->processDeleteById($id);
    }

    /**
     * Cancel a specified return order by id.
     *
     * @param int $id
     * @return bool
     */
    public function cancel($id){
        try{
            $returnOrder = $this->get($id);
            if($returnOrder->getStatus() == Status::STATUS_COMPLETED)
                return false;
            $returnOrder->setStatus(Status::STATUS_CANCELED);
            $this->save($returnOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Confirm a specified return order by id.
     *
     * @param int $id
     * @return bool
     */
    public function confirm($id){
        try{
            $returnOrder = $this->get($id);
            $returnOrder->setStatus(Status::STATUS_PROCESSING);
            $this->save($returnOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

    /**
     * Complete a specified return order by id.
     *
     * @param int $id
     * @return bool
     */
    public function complete($id){
        try{
            $returnOrder = $this->get($id);
            $returnOrder->setStatus(Status::STATUS_COMPLETED);
            $this->save($returnOrder);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }

}