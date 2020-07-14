<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PickRequest;

use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;

class PickRequestItem extends \Magento\Framework\Model\AbstractModel implements PickRequestItemInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequestItem');
    }
    
   /**
     * get PickRequestItem id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::PICK_REQUEST_ITEM_ID);
    }

    /**
     * get parent id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->_getData(self::PARENT_PICK_REQUEST_ITEM_ID);
    }

    /**
     * get Pick Request id
     *
     * @return int
     */
    public function getPickRequestId()
    {
        return $this->_getData(self::PICK_REQUEST_ID);
    }

    /**
     * get Item id
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->_getData(self::ITEM_ID);
    }
    
    /**
     * get Parent Item id
     *
     * @return int
     */
    public function getParentItemId()
    {
        return $this->_getData(self::PARENT_ITEM_ID);        
    }
    
    /**
     * get Product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_getData(self::PRODUCT_ID);
    }            
    
    /**
     * get Item SKU
     *
     * @return string
     */
    public function getItemSku()
    {
        return $this->_getData(self::ITEM_SKU);
    }
    
    /**
     * get Item Name
     *
     * @return string
     */
    public function getItemName()
    {
        return $this->_getData(self::ITEM_NAME);
    }

    /**
     * get Request Qty
     *
     * @return float
     */
    public function getRequestQty()
    {
        return $this->_getData(self::REQUEST_QTY);
    }

    /**
     * get Picked Qty
     *
     * @return float
     */
    public function getPickedQty()
    {
        return $this->_getData(self::PICKED_QTY);
    }
    
    /**
     * get need-to-pick qty
     * 
     * @return float
     */
    public function getNeedToPickQty()
    {
        return max(0, $this->getRequestQty() - $this->getPickedQty());
    }
    
    /**
     * get Shelf Location of item in Warehouse
     * 
     * @return string
     */
    public function getShelfLocation()
    {
        return $this->_getData(self::SHELF_LOCATION);
    }

    /**
     * get barcode
     *
     * @return string
     */
    public function getItemBarcode(){
        return $this->_getData(self::ITEM_BARCODE);
    }

    /**
     * set Id
     * 
     * @param int $id
     */
    public function setId($id)
    {
        return $this->setData(self::PICK_REQUEST_ITEM_ID, $id);
    }

    /**
     * set parent Id
     *
     * @param int $parentId
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_PICK_REQUEST_ITEM_ID, $parentId);
    }
    
    /**
     * set pickRequest Id
     * 
     * @param int $pickRequestId
     */
    public function setPickRequestId($pickRequestId)
    {
        return $this->setData(self::PICK_REQUEST_ID, $pickRequestId);
    }

    /**
     * set Item Id
     * 
     * @param int $itemId
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }
    
    /**
     * set Parent Item Id
     * 
     * @param int $itemId
     */
    public function setParentItemId($itemId)
    {
        return $this->setData(self::PARENT_ITEM_ID, $itemId);        
    }
    
    /**
     * set Product Id
     * 
     * @param int $productId
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }
    
    /**
     * set Sku
     * 
     * @param string $productSku
     */
    public function setItemSku($itemSku)
    {
        return $this->setData(self::ITEM_SKU, $itemSku);
    }
    
    /**
     * set Name
     * 
     * @param string $itemName
     */
    public function setItemName($itemName)
    {
        return $this->setData(self::ITEM_NAME, $itemName);
    }
    

    /**
     * set Request Qty
     * 
     * @param float $requestQty
     */
    public function setRequestQty($requestQty)
    {
        return $this->setData(self::REQUEST_QTY, $requestQty);
    }

    /**
     * set Picked Qty
     * 
     * @param float $pickedQty
     */
    public function setPickedQty($pickedQty)
    {
        return $this->setData(self::PICKED_QTY, $pickedQty);
    }

    /**
     * set barcode
     *
     * @param string $barcode
     */
    public function setItemBarcode($barcode){
        return $this->setData(self::ITEM_BARCODE, $barcode);
    }
}