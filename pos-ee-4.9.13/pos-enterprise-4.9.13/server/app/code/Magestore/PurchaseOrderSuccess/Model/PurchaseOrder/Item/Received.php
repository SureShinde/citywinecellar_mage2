<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item;

use \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReceivedInterface;

/**
 * Class Received
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item
 */
class Received extends \Magento\Framework\Model\AbstractModel
    implements PurchaseOrderItemReceivedInterface
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Received');
    }
    
    /**
     * Get purchase order item received id
     *
     * @return int
     */
    public function getPurchaseOrderItemReceivedId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_RECEIVED_ID);
    }

    /**
     * Set purchase order item received id
     *
     * @param int $purchaseOrderItemReceivedId
     * @return $this
     */
    public function setPurchaseOrderItemReceivedId($purchaseOrderItemReceivedId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_RECEIVED_ID, $purchaseOrderItemReceivedId);
    }

    /**
     * Get purchase order item id
     *
     * @return int
     */
    public function getPurchaseOrderItemId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_ID);
    }

    /**
     * Set purchase order item id
     *
     * @param int $purchaseOrderItemId
     * @return $this
     */
    public function setPurchaseOrderItemId($purchaseOrderItemId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_ID, $purchaseOrderItemId);
    }

    /**
     * Get qty received
     *
     * @return float
     */
    public function getQtyReceived(){
        return $this->_getData(self::QTY_RECEIVED);
    }

    /**
     * Set qty received
     *
     * @param float $qtyReceived
     * @return $this
     */
    public function setQtyReceived($qtyReceived){
        return $this->setData(self::QTY_RECEIVED, $qtyReceived);
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy(){
        return $this->getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy){
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * Get received at
     *
     * @return string
     */
    public function getReceivedAt(){
        return $this->_getData(self::RECEIVED_AT);
    }

    /**
     * Set received at
     *
     * @param string $receivedAt
     * @return $this
     */
    public function setReceivedAt($receivedAt){
        return $this->setData(self::RECEIVED_AT, $receivedAt);
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