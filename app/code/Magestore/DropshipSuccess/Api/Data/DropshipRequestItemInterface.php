<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\Data;


interface DropshipRequestItemInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const DROPSHIP_REQUEST_ITEM_ID = 'dropship_request_item_id';
    const DROPSHIP_REQUEST_ID = 'dropship_request_id';
    const ITEM_ID = 'item_id';
    const PARENT_ITEM_ID = 'parent_item_id';
    const ITEM_SKU = 'item_sku';
    const ITEM_NAME = 'item_name';
    const QTY_REQUESTED = 'qty_requested';
    const QTY_SHIPPED = 'qty_shipped';
    const QTY_CANCELED = 'qty_canceled';


    /**#@-*/

    /**
     * Dropship request item id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set dropship request item id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * dropship request id
     *
     * @return int|null
     */
    public function getDropshipRequestId();

    /**
     * Set dropship request id
     *
     * @param int $dropshipRequestId
     * @return $this
     */
    public function setDropshipRequestId($dropshipRequestId);

    /**
     * Sales Item Id
     *
     * @return string|null
     */
    public function getItemId();

    /**
     * Set order item id
     *
     * @param string $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Sales Item sku
     *
     * @return string|null
     */
    public function getItemSku();

    /**
     * Set order item sku
     *
     * @param string $itemSku
     * @return $this
     */
    public function setItemSku($itemSku);

    /**
     * Sales Item name
     *
     * @return string|null
     */
    public function getItemName();

    /**
     * Set order item name
     *
     * @param string $itemName
     * @return $this
     */
    public function setItemName($itemName);

    /**
     * Qty Requested
     *
     * @return float|null
     */
    public function getQtyRequested();

    /**
     * Set Qty Requested
     *
     * @param float $qtyRequested
     * @return $this
     */
    public function setQtyRequested($qtyRequested);

    /**
     * Qty Shipped
     *
     * @return float|null
     */
    public function getQtyShipped();

    /**
     * Set Qty Shipped
     *
     * @param float $qtyShipped
     * @return $this
     */
    public function setQtyShipped($qtyShipped);

    /**
     * Qty Canceled
     *
     * @return float|null
     */
    public function getQtyCanceled();

    /**
     * Qty Canceled
     *
     * @param float $qtyCanceled
     * @return $this
     */
    public function setQtyCanceled($qtyCanceled);
}