<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

interface PickRequestItemInterface 
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PICK_REQUEST_ITEM_ID = 'pick_request_item_id';    
    const PARENT_PICK_REQUEST_ITEM_ID = 'parent_pick_request_item_id';
    const PICK_REQUEST_ID = 'pick_request_id';
    const ITEM_ID = 'item_id';    
    const PARENT_ITEM_ID = 'parent_item_id';    
    const PRODUCT_ID = 'product_id';    
    const ITEM_SKU = 'item_sku';    
    const ITEM_NAME= 'item_name';    
    const REQUEST_QTY = 'request_qty';    
    const PICKED_QTY = 'picked_qty';    
    const SHELF_LOCATION = 'shelf_location';
    const ITEM_BARCODE = 'item_barcode';

    /**
     * get PickRequestItem id
     *
     * @return int|null
     */
    public function getId();   

    /**
     * get Pick Request id
     *
     * @return int
     */
    public function getPickRequestId();

    /**
     * get parent Pick Request item id
     *
     * @return int
     */
    public function getParentId();

    /**
     * get Item id
     *
     * @return int
     */
    public function getItemId();   
    
    /**
     * get Parent Item id
     *
     * @return int
     */
    public function getParentItemId();   
        
    /**
     * get Product id
     *
     * @return int
     */
    public function getProductId();       
    
    /**
     * get Item SKU
     *
     * @return string
     */
    public function getItemSku();      
    
    /**
     * get Item Name
     *
     * @return string
     */
    public function getItemName();         

    /**
     * get Request Qty
     *
     * @return float
     */
    public function getRequestQty();   

    /**
     * get Picked Qty
     *
     * @return float
     */
    public function getPickedQty();   
    
    /**
     * get need-to-pick qty
     * 
     * @return float
     */
    public function getNeedToPickQty();    
    
    /**
     * get Shelf Location of item in Warehouse
     * 
     * @return string
     */
    public function getShelfLocation();

    /**
     * get barcode
     *
     * @return string
     */
    public function getItemBarcode();

    /**
     * set Id
     * 
     * @param int $id
     */
    public function setId($id);

    /**
     * set parent item Id
     *
     * @param int $parentId
     */
    public function setParentId($parentId);
    
    /**
     * set pickRequest Id
     * 
     * @param int $pickRequestId
     */
    public function setPickRequestId($pickRequestId);

    /**
     * set Item Id
     * 
     * @param int $itemId
     */
    public function setItemId($itemId);
    
    /**
     * set Parent Item Id
     * 
     * @param int $itemId
     */
    public function setParentItemId($itemId);    
    
    /**
     * set Product Id
     * 
     * @param int $productId
     */
    public function setProductId($productId); 
    
    /**
     * set Sku
     * 
     * @param string $itemSku
     */
    public function setItemSku($itemSku);           
    
    /**
     * set Name
     * 
     * @param string $itemName
     */
    public function setItemName($itemName);        

    /**
     * set Request Qty
     * 
     * @param float $requestQty
     */
    public function setRequestQty($requestQty);

    /**
     * set Picked Qty
     * 
     * @param float $pickedQty
     */
    public function setPickedQty($pickedQty);

    /**
     * set barcode
     *
     * @param string $barcode
     */
    public function setItemBarcode($barcode);
    
}