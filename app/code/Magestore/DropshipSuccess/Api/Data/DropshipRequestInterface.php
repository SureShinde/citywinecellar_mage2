<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\Data;


interface DropshipRequestInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const DROPSHIP_REQUEST_ID = 'dropship_request_id';
    const ORDER_ID = 'order_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const SUPPLIER_ID = 'supplier_id';
    const SUPPLIER_NAME = 'supplier_name';
    const TOTAL_REQUESTED = 'total_requested';
    const TOTAL_SHIPPED = 'total_shipped';
    const TOTAL_CANCELED = 'total_canceled';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const CURRENT_DROPSHIP_REQUEST = 'current_dropship_request';

    const STATUS_PENDING = 0;
    const STATUS_PARTIAL_SHIP = 1;
    const STATUS_SHIPPED = 2;
    const STATUS_CANCELED = 3;

    /**#@-*/

    /**
     * Dropship request id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set dropship request id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * order id
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Sales Increment Id
     *
     * @return string|null
     */
    public function getOrderIncrementId();

    /**
     * Set order increment id
     *
     * @param string $orderIncrementId
     * @return $this
     */
    public function setOrderIncrementId($orderIncrementId);

    /**
     * Supplier Id
     *
     * @return int|null
     */
    public function getSupplierId();

    /**
     * Set supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId);

    /**
     * Supplier Name
     *
     * @return string|null
     */
    public function getSupplierName();

    /**
     * Set Supplier Name
     *
     * @param string $supplierName
     * @return $this
     */
    public function setSupplierName($supplierName);

    /**
     * Total Requested
     *
     * @return float|null
     */
    public function getTotalRequested();

    /**
     * Set Total Requested
     *
     * @param string $totalRequested
     * @return $this
     */
    public function setTotalRequested($totalRequested);

    /**
     * Total Shipped
     *
     * @return float|null
     */
    public function getTotalShipped();

    /**
     * Set Total Shipped
     *
     * @param float $totalShipped
     * @return $this
     */
    public function setTotalShipped($totalShipped);

    /**
     * Total Canceled
     *
     * @return float|null
     */
    public function getTotalCanceled();

    /**
     * Total Canceled
     *
     * @param float $totalCanceled
     * @return $this
     */
    public function setTotalCanceled($totalCanceled);

    /**
     * Status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return array
     */
    public function getStatusOption();
}