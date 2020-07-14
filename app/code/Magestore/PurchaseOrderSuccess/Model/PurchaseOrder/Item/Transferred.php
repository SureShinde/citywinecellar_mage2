<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item;

use \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemTransferredInterface;

/**
 * Class Transferred
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item
 */
class Transferred extends \Magento\Framework\Model\AbstractModel
    implements PurchaseOrderItemTransferredInterface
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
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Transferred');
    }

    /**
     * Get purchase order item transferred id
     *
     * @return int
     */
    public function getPurchaseOrderItemTransferredId(){
        return $this->_getData(self::PURCHASE_ORDER_ITEM_TRANSFERRED_ID);
    }

    /**
     * Set purchase order item transferred id
     *
     * @param int $purchaseOrderItemTransferredId
     * @return $this
     */
    public function setPurchaseOrderItemTransferredId($purchaseOrderItemTransferredId){
        return $this->setData(self::PURCHASE_ORDER_ITEM_TRANSFERRED_ID, $purchaseOrderItemTransferredId);
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
     * Get qty transferred
     *
     * @return float
     */
    public function getQtyTransferred(){
        return $this->_getData(self::QTY_TRANSFERRED);
    }

    /**
     * Set qty transferred
     *
     * @param float $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred){
        return $this->setData(self::QTY_TRANSFERRED, $qtyTransferred);
    }

    /**
     * Get warehouse id
     *
     * @return int
     */
    public function getWarehouseId(){
        return $this->_getData(self::WAREHOUSE_ID);
    }

    /**
     * Set warehouse id
     *
     * @param float $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId){
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * Get transferred at
     *
     * @return string
     */
    public function getTransferredAt(){
        return $this->_getData(self::TRANSFERRED_AT);
    }

    /**
     * Set transferred at
     *
     * @param string $transferredAt
     * @return $this
     */
    public function setTransferredAt($transferredAt){
        return $this->setData(self::TRANSFERRED_AT, $transferredAt);
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