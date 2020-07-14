<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment;

/**
 * Class Item
 * @package Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment
 */
class Item extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\DropshipSuccess\Api\Data\DropshipShipmentItemInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Item');
    }

    /**#@-*/

    /**
     * Dropship shipment id id
     *
     * @return int|null
     */
    public function getDropshipShipmentItemId(){
        return $this->_getData(self::DROPSHIP_SHIPMENT_ITEM_ID);
    }

    /**
     * Set dropship shipment item id
     *
     * @param int $dropshipShipmentItemId
     * @return $this
     */
    public function setDropshipShipmentItemId($dropshipShipmentItemId){
        return $this->setData(self::DROPSHIP_SHIPMENT_ITEM_ID, $dropshipShipmentItemId);
    }

    /**
     * Dropship shipment id
     *
     * @return int|null
     */
    public function getDropshipShipmentId(){
        return $this->_getData(self::DROPSHIP_SHIPMENT_ID);
    }

    /**
     * Set dropship shipment id
     *
     * @param int $dropshipShipmentId
     * @return $this
     */
    public function setDropshipShipmentId($dropshipShipmentId){
        return $this->setData(self::DROPSHIP_SHIPMENT_ID, $dropshipShipmentId);
    }

    /**
     * Shipment item Id
     *
     * @return int|null
     */
    public function getItemId(){
        return $this->_getData(self::ITEM_ID);
    }

    /**
     * Set shipment item id
     *
     * @param string $itemId
     * @return $this
     */
    public function setItemId($itemId){
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * Shipment item sku
     *
     * @return string
     */
    public function getItemSku(){
        return $this->_getData(self::ITEM_SKU);
    }

    /**
     * Set shipment item sku
     *
     * @param string $itemSku
     * @return $this
     */
    public function setItemSku($itemSku){
        return $this->setData(self::ITEM_SKU, $itemSku);
    }

    /**
     * Shipment item name
     *
     * @return string
     */
    public function getItemName(){
        return $this->_getData(self::ITEM_NAME);
    }

    /**
     * Set shipment item name
     *
     * @param string $itemName
     * @return $this
     */
    public function setItemName($itemName){
        return $this->setData(self::ITEM_NAME, $itemName);
    }

    /**
     * Qty shipped
     *
     * @return float|null
     */
    public function getQtyShipped(){
        return $this->_getData(self::QTY_SHIPPED);
    }

    /**
     * Set qty shipped
     *
     * @param float $qtyShipped
     * @return $this
     */
    public function setQtyShipped($qtyShipped){
        return $this->setData(self::QTY_SHIPPED, $qtyShipped);
    }
}