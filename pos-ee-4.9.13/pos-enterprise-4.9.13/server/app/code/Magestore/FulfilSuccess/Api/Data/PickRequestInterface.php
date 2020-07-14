<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

interface PickRequestInterface
{
    /*     * #@+
     * Constants defined for keys of  data array
     */

    const PICK_REQUEST_ID = 'pick_request_id';
    const PACK_REQUEST_ID = 'pack_request_id';
    const ORDER_ID = 'order_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const WAREHOUSE_ID = 'warehouse_id';
    const AGE = 'age';
    const STATUS = 'status';
    const BATCH_ID = 'batch_id';
    const USER_ID = 'user_id';
    const TOTAL_ITEMS = 'total_items';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const SOURCE_CODE = 'source_code';

    /**
     * Define Pick Request Status
     */
    const STATUS_PICKING = 0;
    const STATUS_PICKED = 1;
    const STATUS_CANCELED = 2;
    
    
    /**
     * get Pick Request id
     *
     * @return int|null
     */
    public function getId();

    /**
     * get Pick Request id
     *
     * @return int|null
     */
    public function getPickRequestId();
    
    /**
     * get Pack Request id
     *
     * @return int|null
     */
    public function getPackRequestId();    

    /**
     * get Sales id
     *
     * @return int
     */
    public function getOrderId();
    
    /**
     * get Sales increment id
     *
     * @return string
     */
    public function getOrderIncrementId();    

    /**
     * get warehouse Id
     *
     * @return int
     */
    public function getWarehouseId();

    /**
     * get age
     *
     * @return int
     */
    public function getAge();
    
    /**
     * get batch Id
     *
     * @return int
     */
    public function getBatchId();    
    
    /**
     * get user Id
     *
     * @return int
     */
    public function getUserId();        
    
    /**
     * get count total items
     *
     * @return float
     */
    public function getTotalItems();        
    
    /**
     * get Status
     *
     * @return int
     */
    public function getStatus();    

    /**
     * get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * set Id
     * 
     * @param int $id
     */
    public function setId($id);

    /**
     * set PickRequest Id
     * 
     * @param int $pickRequestId
     */
    public function setPickRequestId($pickRequestId);
    
    /**
     * set PackRequest Id
     * 
     * @param int $packRequestId
     */
    public function setPackRequestId($packRequestId);    

    /**
     * set Sales ID
     * 
     * @param int $orderId
     */
    public function setOrderId($orderId);
    
    /**
     * set Sales Increment ID
     * 
     * @param string $orderIncrementId
     */
    public function setOrderIncrementId($orderIncrementId);
    

    /**
     * set Warehouse Id
     * 
     * @param int $warehouseId
     */
    public function setWarehouseId($warehouseId);

    /**
     * set Age
     * 
     * @param int $age
     */
    public function setAge($age);

    /**
     * set Batch Id
     * 
     * @param int $batchId
     */
    public function setBatchId($batchId);    
    
    /**
     * set User Id
     * 
     * @param int $userId
     */
    public function setUserId($userId);        
    
    /**
     * set count total items
     *
     * @param float $totalItems
     */
    public function setTotalItems($totalItems);         
    
    /**
     * set Status
     * 
     * @param int $status
     */
    public function setStatus($status);    

    /**
     * set Created Time
     * 
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * set Updated Time
     *
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt);
    
    /**
     * get updated at
     *
     * @return string
     */
    public function getSourceCode();

    /**
     * set Source Code
     *
     * @param string $sourceCode
     */
    public function setSourceCode($sourceCode);
}
