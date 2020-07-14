<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PickRequest;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;

class PickRequest extends \Magento\Framework\Model\AbstractModel implements PickRequestInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest');
    }

    /**
     * get Pick Request id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::PICK_REQUEST_ID);
    }

    /**
     * get Pick Request id
     *
     * @return int|null
     */
    public function getPickRequestId()
    {
        return $this->_getData(self::PICK_REQUEST_ID);
    }

    /**
     * get Pack Request id
     *
     * @return int|null
     */
    public function getPackRequestId()
    {
        return $this->_getData(self::PACK_REQUEST_ID);
    }

    /**
     * get Order id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * get Sales increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->_getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * get warehouse Id
     *
     * @return int
     */
    public function getWarehouseId()
    {
        return $this->_getData(self::WAREHOUSE_ID);
    }

    /**
     * get age
     *
     * @return int
     */
    public function getAge()
    {
        return $this->_getData(self::AGE);
    }

    /**
     * get batch Id
     *
     * @return int
     */
    public function getBatchId()
    {
        return $this->_getData(self::BATCH_ID);
    }

    /**
     * get user Id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_getData(self::USER_ID);
    }

    /**
     * get count total items
     *
     * @return float
     */
    public function getTotalItems()
    {
        return $this->_getData(self::TOTAL_ITEMS);
    }

    /**
     * get Status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * get updated at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * set Id
     *
     * @param int $id
     */
    public function setId($id)
    {
        return $this->setData(self::PICK_REQUEST_ID, $id);
    }

    /**
     * set PickRequest Id
     *
     * @param int $pickRequestId
     */
    public function setPickRequestId($pickRequestId)
    {
        return $this->setData(self::PICK_REQUEST_ID, $pickRequestId);
    }

    /**
     * set PackRequest Id
     *
     * @param int $packRequestId
     */
    public function setPackRequestId($packRequestId)
    {
        return $this->setData(self::PACK_REQUEST_ID, $packRequestId);
    }

    /**
     * set Sales ID
     *
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * set Sales Increment ID
     *
     * @param string $orderIncrementId
     */
    public function setOrderIncrementId($orderIncrementId)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
    }

    /**
     * set Warehouse Id
     *
     * @param int $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * set Age
     *
     * @param int $age
     */
    public function setAge($age)
    {
        return $this->setData(self::AGE, $age);
    }

    /**
     * set Batch Id
     *
     * @param int $batchId
     */
    public function setBatchId($batchId)
    {
        return $this->setData(self::BATCH_ID, $batchId);
    }

    /**
     * set User Id
     *
     * @param int $userId
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * set count total items
     *
     * @param float $totalItems
     */
    public function setTotalItems($totalItems)
    {
        return $this->setData(self::TOTAL_ITEMS, $totalItems);
    }

    /**
     * set Status
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * set Created Time
     *
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * set Updated Time
     *
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * get updated at
     *
     * @return string
     */
    public function getSourceCode()
    {
        return $this->getData(self::SOURCE_CODE);
    }

    /**
     * set Source Code
     *
     * @param string $sourceCode
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(self::SOURCE_CODE, $sourceCode);
    }
}