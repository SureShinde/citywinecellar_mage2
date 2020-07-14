<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item;

use \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemReturnedInterface;

/**
 * Class Transferred
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item
 */
class Returned extends \Magento\Framework\Model\AbstractModel
    implements PurchaseOrderItemReturnedInterface
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Returned');
    }

    /**
     * Get purchase order item returned id
     *
     * @return int
     */
    public function getPurchaseOrderItemReturnedId()
    {
        return $this->_getData(self::PURCHASE_ORDER_ITEM_RETURNED_ID);
    }
    
    /**
     * Set purchase order item returned id
     *
     * @param int $purchaseOrderItemReturnedId
     * @return $this
     */
    public function setPurchaseOrderItemReturnedId($purchaseOrderItemReturnedId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_RETURNED_ID, $purchaseOrderItemReturnedId);
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
     * Get qty returned
     *
     * @return float
     */
    public function getQtyReturned(){
        return $this->_getData(self::QTY_RETURNED);
    }

    /**
     * Set qty returned
     *
     * @param float $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned){
        return $this->setData(self::QTY_RETURNED, $qtyReturned);
    }

    /**
     * Get returned at
     *
     * @return string
     */
    public function getReturnedAt(){
        return $this->_getData(self::RETURNED_AT);
    }

    /**
     * Set returned at
     *
     * @param string $returnedAt
     * @return $this
     */
    public function setReturnedAt($returnedAt){
        return $this->setData(self::RETURNED_AT, $returnedAt);
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