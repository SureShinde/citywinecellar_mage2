<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PurchaseOrderItemReturnedInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    
    const PURCHASE_ORDER_ITEM_RETURNED_ID = 'purchase_order_item_returned_id';
    
    const PURCHASE_ORDER_ITEM_ID = 'purchase_order_item_id';
    
    const QTY_RETURNED = 'qty_returned';
    
    const RETURNED_AT = 'returned_at';
    
    const CREATED_AT = 'created_at';

    /**#@-*/

    /**
     * Get purchase order item returned id
     *
     * @return int
     */
    public function getPurchaseOrderItemReturnedId();

    /**
     * Set purchase order item returned id
     *
     * @param int $purchaseOrderItemReturnedId
     * @return $this
     */
    public function setPurchaseOrderItemReturnedId($purchaseOrderItemReturnedId);

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
     * Get qty returned
     *
     * @return float
     */
    public function getQtyReturned();

    /**
     * Set qty returned
     *
     * @param float $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned);

    /**
     * Get returned at
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