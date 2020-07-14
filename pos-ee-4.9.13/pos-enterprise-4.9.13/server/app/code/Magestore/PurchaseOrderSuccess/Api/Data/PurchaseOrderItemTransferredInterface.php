<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PurchaseOrderItemTransferredInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_ITEM_TRANSFERRED_ID = 'purchase_order_item_transferred_id';
    
    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';
    
    const QTY_TRANSFERRED = 'qty_transferred';
    
    const WAREHOUSE_ID = 'warehouse_id';
    
    const TRANSFERRED_AT = 'transferred_at';
    
    const CREATED_AT = 'created_at';

    /**#@-*/

    /**
     * Get purchase order item transferred id
     *
     * @return int
     */
    public function getPurchaseOrderItemTransferredId();

    /**
     * Set purchase order item transferred id
     *
     * @param int $purchaseOrderItemTransferredId
     * @return $this
     */
    public function setPurchaseOrderItemTransferredId($purchaseOrderItemTransferredId);

    /**
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId();

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId);

    /**
     * Get qty transferred
     *
     * @return float
     */
    public function getQtyTransferred();

    /**
     * Set qty transferred
     *
     * @param float $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred);

    /**
     * Get warehouse id
     *
     * @return int
     */
    public function getWarehouseId();

    /**
     * Set warehouse id
     *
     * @param float $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId);

    /**
     * Get transferred at
     *
     * @return string
     */
    public function getTransferredAt();

    /**
     * Set transferred at
     *
     * @param string $transferredAt
     * @return $this
     */
    public function setTransferredAt($transferredAt);

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
}