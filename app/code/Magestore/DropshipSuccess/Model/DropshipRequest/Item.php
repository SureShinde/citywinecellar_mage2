<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\DropshipRequest;

/**
 * Class Item
 * @package Magestore\DropshipSuccess\Model\DropshipRequest
 */
class Item extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item');
    }

    /**#@-*/

    /**
     * Dropship request item id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData('dropship_request_item_id');
    }

    /**
     * Set dropship request item id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('dropship_request_item_id', $id);
    }

    /**
     * dropship request id
     *
     * @return int|null
     */
    public function getDropshipRequestId()
    {
        return $this->_getData(self::DROPSHIP_REQUEST_ID);
    }

    /**
     * Set dropship request id
     *
     * @param int $dropshipRequestId
     * @return $this
     */
    public function setDropshipRequestId($dropshipRequestId)
    {
        return $this->setData(self::DROPSHIP_REQUEST_ID, $dropshipRequestId);
    }

    /**
     * Sales Item Id
     *
     * @return string|null
     */
    public function getItemId()
    {
        return $this->_getData(self::ITEM_ID);
    }

    /**
     * Set order item id
     *
     * @param string $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * Order Parent Item Id
     *
     * @return string|null
     */
    public function getParentItemId()
    {
        return $this->_getData(self::PARENT_ITEM_ID);
    }

    /**
     * Set order parent item id
     *
     * @param string $parentItemId
     * @return $this
     */
    public function setParentItemId($parentItemId)
    {
        return $this->setData(self::PARENT_ITEM_ID, $parentItemId);
    }

    /**
    * Order Item sku
    *
    * @return string|null
    */
    public function getItemSku()
    {
        return $this->_getData(self::ITEM_SKU);
    }

    /**
     * Set order item sku
     *
     * @param string $itemSku
     * @return $this
     */
    public function setItemSku($itemSku)
    {
        return $this->setData(self::ITEM_SKU, $itemSku);
    }

    /**
     * Sales Item name
     *
     * @return string|null
     */
    public function getItemName()
    {
        return $this->_getData(self::ITEM_NAME);
    }

    /**
     * Set order item name
     *
     * @param string $itemName
     * @return $this
     */
    public function setItemName($itemName)
    {
        return $this->setData(self::ITEM_NAME, $itemName);
    }

    /**
     * Qty Requested
     *
     * @return float|null
     */
    public function getQtyRequested()
    {
        return $this->_getData(self::QTY_REQUESTED);
    }

    /**
     * Set Qty Requested
     *
     * @param float $qtyRequested
     * @return $this
     */
    public function setQtyRequested($qtyRequested)
    {
        return $this->setData(self::QTY_REQUESTED, $qtyRequested);
    }

    /**
     * Qty Shipped
     *
     * @return float|null
     */
    public function getQtyShipped()
    {
        return $this->_getData(self::QTY_SHIPPED);
    }

    /**
     * Set Qty Shipped
     *
     * @param float $qtyShipped
     * @return $this
     */
    public function setQtyShipped($qtyShipped)
    {
        return $this->setData(self::QTY_SHIPPED, $qtyShipped);
    }

    /**
     * Qty Canceled
     *
     * @return float|null
     */
    public function getQtyCanceled()
    {
        return $this->_getData(self::QTY_CANCELED);
    }

    /**
     * Qty Canceled
     *
     * @param float $qtyCanceled
     * @return $this
     */
    public function setQtyCanceled($qtyCanceled)
    {
        return $this->setData(self::QTY_CANCELED, $qtyCanceled);
    }
}