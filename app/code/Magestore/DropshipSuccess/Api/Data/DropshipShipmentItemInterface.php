<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\Data;

/**
 * Interface DropshipShipmentItemInterface
 * @package Magestore\DropshipSuccess\Api\Data
 */
interface DropshipShipmentItemInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const DROPSHIP_SHIPMENT_ITEM_ID = 'dropship_shipment_item_id';
    const DROPSHIP_SHIPMENT_ID = 'dropship_shipment_id';
    const ITEM_ID = 'item_id';
    const ITEM_SKU = 'item_sku';
    const ITEM_NAME = 'item_name';
    const QTY_SHIPPED = 'qty_shipped';
    
    /**#@-*/
    
    /**
     * Dropship shipment id id
     *
     * @return int|null
     */
    public function getDropshipShipmentItemId();

    /**
     * Set dropship shipment item id
     *
     * @param int $dropshipShipmentItemId
     * @return $this
     */
    public function setDropshipShipmentItemId($dropshipShipmentItemId);
    
    /**
     * Dropship shipment id
     *
     * @return int|null
     */
    public function getDropshipShipmentId();

    /**
     * Set dropship shipment id
     *
     * @param int $dropshipShipmentId
     * @return $this
     */
    public function setDropshipShipmentId($dropshipShipmentId);

    /**
     * Shipment item Id
     *
     * @return int|null
     */
    public function getItemId();

    /**
     * Set shipment item id
     *
     * @param string $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Shipment item sku
     *
     * @return string
     */
    public function getItemSku();

    /**
     * Set shipment item sku
     *
     * @param string $itemSku
     * @return $this
     */
    public function setItemSku($itemSku);

    /**
     * Shipment item name
     *
     * @return string
     */
    public function getItemName();

    /**
     * Set shipment item name
     *
     * @param string $itemName
     * @return $this
     */
    public function setItemName($itemName);

    /**
     * Qty shipped
     *
     * @return float|null
     */
    public function getQtyShipped();

    /**
     * Set qty shipped
     *
     * @param float $qtyShipped
     * @return $this
     */
    public function setQtyShipped($qtyShipped);
}