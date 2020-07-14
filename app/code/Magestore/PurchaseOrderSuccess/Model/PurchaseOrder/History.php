<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder;

use \Magestore\PurchaseOrderSuccess\Api\Data\HistoryInterface;

/**
 * Class History
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder
 */
class History extends \Magento\Framework\Model\AbstractModel
    implements HistoryInterface
{
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\History');
    }
    
    /**
     * Get purchase order history id
     *
     * @return int
     */
    public function getPurchaseOrderHistoryId(){
        return $this->_getData(self::PURCHASE_ORDER_HISTORY_ID);
    }

    /**
     * Set purchase order history id
     *
     * @param int $purchaseOrderHistoryId
     * @return $this
     */
    public function setPurchaseOrderHistoryId($purchaseOrderHistoryId){
        return $this->setData(self::PURCHASE_ORDER_HISTORY_ID, $purchaseOrderHistoryId);
    }

    /**
     * Get purchase order id
     *
     * @return int
     */
    public function getPurchaseOrderId(){
        return $this->_getData(self::PURCHASE_ORDER_ID);
    }

    /**
     * Set purchase order id
     *
     * @param int $purchaseOrderId
     * @return $this
     */
    public function setPurchaseOrderId($purchaseOrderId){
        return $this->setData(self::PURCHASE_ORDER_ID, $purchaseOrderId);
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName(){
        return $this->_getData(self::USER_NAME);
    }

    /**
     * Set user name
     *
     * @param string $userName
     * @return $this
     */
    public function setUserName($userName){
        return $this->setData(self::USER_NAME, $userName);
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId(){
        return $this->_getData(self::USER_ID);
    }

    /**
     * Set user id
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId){
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent(){
        return $this->_getData(self::CONTENT);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content){
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Get old value
     *
     * @return string
     */
    public function getOldValue(){
        return $this->_getData(self::OLD_VALUE);
    }

    /**
     * Set old value
     *
     * @param string $oldValue
     * @return $this
     */
    public function setOldValue($oldValue){
        return $this->setData(self::OLD_VALUE, $oldValue);
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getNewValue(){
        return $this->_getData(self::NEW_VALUE);
    }

    /**
     * Set user name
     *
     * @param string $userName
     * @return $this
     */
    public function setNewValue($newValue){
        return $this->setData(self::NEW_VALUE, $newValue);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(){
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}