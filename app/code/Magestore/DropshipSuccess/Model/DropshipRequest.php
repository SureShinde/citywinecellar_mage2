<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model;

/**
 * Class DropshipRequest
 * @package Magestore\DropshipSuccess\Model
 */
class DropshipRequest extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest');
    }

    /**
     * Identifier getter
     *
     * @return int
     */
    public function getId()
    {
        return $this->_getData('dropship_request_id');
    }

    /**
     * Set entity Id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setData('dropship_request_id', $value);
    }

    /**
     * order id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * Set order id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Sales Increment Id
     *
     * @return string|null
     */
    public function getOrderIncrementId()
    {
        return $this->_getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * Set order increment id
     *
     * @param string $orderIncrementId
     * @return $this
     */
    public function setOrderIncrementId($orderIncrementId)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
    }

    /**
     * Supplier Id
     *
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId)
    {
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Supplier Name
     *
     * @return string|null
     */
    public function getSupplierName()
    {
        return $this->_getData(self::SUPPLIER_NAME);
    }

    /**
     * Set Supplier Name
     *
     * @param string $supplierName
     * @return $this
     */
    public function setSupplierName($supplierName)
    {
        return $this->setData(self::SUPPLIER_NAME, $supplierName);
    }

    /**
     * Total Requested
     *
     * @return float|null
     */
    public function getTotalRequested()
    {
        return $this->_getData(self::TOTAL_REQUESTED);
    }

    /**
     * Set Total Requested
     *
     * @param string $totalRequested
     * @return $this
     */
    public function setTotalRequested($totalRequested)
    {
        return $this->setData(self::TOTAL_REQUESTED, $totalRequested);
    }

    /**
     * Total Shipped
     *
     * @return float|null
     */
    public function getTotalShipped()
    {
        return $this->_getData(self::TOTAL_SHIPPED);
    }

    /**
     * Set Total Shipped
     *
     * @param float $totalShipped
     * @return $this
     */
    public function setTotalShipped($totalShipped)
    {
        return $this->setData(self::TOTAL_SHIPPED, $totalShipped);
    }

    /**
     * Total Canceled
     *
     * @return float|null
     */
    public function getTotalCanceled()
    {
        return $this->_getData(self::TOTAL_CANCELED);
    }

    /**
     * Total Canceled
     *
     * @param float $totalCanceled
     * @return $this
     */
    public function setTotalCanceled($totalCanceled)
    {
        return $this->setData(self::TOTAL_CANCELED, $totalCanceled);
    }

    /**
     * Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set Created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return array
     */
    public function getStatusOption()
    {
        $array = [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_PARTIAL_SHIP => __('Partial Ship'),
            self::STATUS_SHIPPED => __('Completed'),
            self::STATUS_CANCELED => __('Canceled'),
        ];
        return $array;
    }
}