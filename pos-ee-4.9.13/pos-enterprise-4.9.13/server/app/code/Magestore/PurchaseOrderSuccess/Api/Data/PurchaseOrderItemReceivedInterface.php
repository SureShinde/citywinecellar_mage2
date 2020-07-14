<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PurchaseOrderItemReceivedInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_ITEM_RECEIVED_ID = 'purchase_order_item_received_id';
    
    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';
    
    const QTY_RECEIVED = 'qty_received';
    
    const CREATED_BY = 'created_by';
    
    const RECEIVED_AT = 'received_at';
    
    const CREATED_AT = 'created_at';

    /**#@-*/

    /**
     * Get purchase order item received id
     *
     * @return int
     */
    public function getPurchaseOrderItemReceivedId();

    /**
     * Set purchase order item received id
     *
     * @param int $purchaseOrderItemReceivedId
     * @return $this
     */
    public function setPurchaseOrderItemReceivedId($purchaseOrderItemReceivedId);

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
     * Get qty received
     *
     * @return float
     */
    public function getQtyReceived();

    /**
     * Set qty received
     *
     * @param float $qtyReceived
     * @return $this
     */
    public function setQtyReceived($qtyReceived);

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
     * Get received at
     *
     * @return string
     */
    public function getReceivedAt();

    /**
     * Set received at
     *
     * @param string $receivedAt
     * @return $this
     */
    public function setReceivedAt($receivedAt);

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