<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api\Data;

interface PackRequestInterface
{
    CONST PACK_REQUEST_ID = 'pack_request_id';
    CONST PICK_REQUEST_ID = 'pick_request_id';
    CONST USER_ID = 'user_id';  
    CONST ORDER_ID = 'order_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    CONST WAREHOUSE_ID = 'warehouse_id';
    CONST AGE = 'age';
    CONST TOTAL_ITEMS = 'total_items';
    CONST CREATED_AT = 'created_at';
    CONST UPDATED_AT = 'updated_at';
    CONST STATUS = 'status';
    CONST ITEMS = 'items';
    CONST PACKAGES = 'packages';
    const SOURCE_CODE = 'source_code';

    /**
     * Define Pack Request Status
     */
    const STATUS_PACKING = 0;
    const STATUS_PARTIAL_PACK = 1;
    const STATUS_PACKED = 2;
    const STATUS_CANCELED = 3;

    /**
     * @return int
     */
    public function getPackRequestId();

    /**
     * @param $packRequestId int
     * @return $this
     */
    public function setPackRequestId($packRequestId);

    /**
     * @return int
     */
    public function getPickRequestId();

    /**
     * @param $pickRequestId int
     * @return $this
     */
    public function setPickRequestId($pickRequestId);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param $userId int
     * @return $this
     */
    public function setUserId($userId);
    
    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param $orderId int
     * @return $this
     */
    public function setOrderId($orderId);    

    /**
     * get Sales increment id
     *
     * @return string
     */
    public function getOrderIncrementId();

    /**
     * set Sales Increment ID
     *
     * @param string $orderIncrementId
     */
    public function setOrderIncrementId($orderIncrementId);

    /**
     * @return int
     */
    public function getWarehouseId();

    /**
     * @param $warehouseId int
     * @return $this
     */
    public function setWarehouseId($warehouseId);

    /**
     * @return int
     */
    public function getAge();

    /**
     * @param $age int
     * @return $this
     */
    public function setAge($age);

    /**
     * @return int
     */
    public function getTotalItems();

    /**
     * @param $totalItems
     * @return $this
     */
    public function setTotalItems($totalItems);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $createdAt string
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param $updatedAt string
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param $status int
     * @return $this
     */
    public function setStatus($status);

    /**
     * Gets items for the pack request.
     *
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface[] Array of items.
     */
    public function getItems();

    /**
     * Gets packages for the pack request.
     *
     * @return \Magestore\FulfilSuccess\Api\Data\PackageInterface[] Array of packages.
     */
    public function getPackages();

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