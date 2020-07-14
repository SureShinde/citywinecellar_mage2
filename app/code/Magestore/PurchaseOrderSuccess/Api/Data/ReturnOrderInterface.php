<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface ReturnOrderInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const RETURN_ORDER_ID = 'return_id';

    const RETURN_CODE = 'return_code';

    const WAREHOUSE_ID = 'warehouse_id';

    const SUPPLIER_ID = 'supplier_id';

    const TYPE = 'type';

    const STATUS = 'status';

    const REASON = 'reason';

    const USER_ID = 'user_id';

    const CREATED_BY = 'created_by';

    const TOTAL_QTY_TRANSFERRED = 'total_qty_transferred';

    const TOTAL_QTY_RETURNED = 'total_qty_returned';

    const RETURNED_AT = 'returned_at';

    const CANCELED_AT = 'canceled_at';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const ITEMS = 'items';

    /**#@-*/

    /**
     * Get return order id
     *
     * @return int
     */
    public function getReturnOrderId();

    /**
     * Set return order id
     *
     * @param int $returnOrderId
     * @return $this
     */
    public function setReturnOrderId($returnOrderId);

    /**
     * Get return code
     *
     * @return string|null
     */
    public function getReturnCode();

    /**
     * Set return code
     *
     * @param string $returnCode
     * @return $this
     */
    public function setReturnCode($returnCode);

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getSupplierId();

    /**
     * Set supplier id
     *
     * @param int $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId);

    /**
     * Get supplier id
     *
     * @return int
     */
    public function getWarehouseId();

    /**
     * Set warehouse id
     *
     * @param int $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId);

    /**
     * Get type
     *
     * @return int
     */
    public function getType();

    /**
     * Set type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason();

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason);

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set user id
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy();

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);

    /**
     * Get total qty transferred
     *
     * @return float
     */
    public function getTotalQtyTransferred();

    /**
     * Set total qty transferred
     *
     * @param float $totalQtyTransferred
     * @return $this
     */
    public function setTotalQtyTransferred($totalQtyTransferred);

    /**
     * Get total qty returned
     *
     * @return float
     */
    public function getTotalQtyReturned();

    /**
     * Set total qty returned
     *
     * @param float $totalQtyReturned
     * @return $this
     */
    public function setTotalQtyReturned($totalQtyReturned);

    /**
     * Get returnd at
     *
     * @return string
     */
    public function getReturnedAt();

    /**
     * Set returned at
     *
     * @param string $returnedAt
     * @return $this
     */
    public function setReturnedAt($returnedAt);

    /**
     * Get canceled at
     *
     * @return string
     */
    public function getCanceledAt();

    /**
     * Set canceled at
     *
     * @param string $canceledAt
     * @return $this
     */
    public function setCanceledAt($canceledAt);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get return order item
     *
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface[]
     */
    public function getItems();

    /**
     * Set return order item
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface[] $item
     * @return $this
     */
    public function setItems($item);
}