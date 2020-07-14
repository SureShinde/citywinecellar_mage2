<?php

namespace Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item;

use \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface;

/**
 * Class Transferred
 * @package Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Item
 */
class Transferred extends \Magento\Framework\Model\AbstractModel
    implements ReturnOrderItemTransferredInterface
{

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred');
    }

    /**
     * Get return order item transferred id
     *
     * @return int
     */
    public function getReturnItemTransferredId() {
        return $this->_getData(self::RETURN_ITEM_TRANSFERRED_ID);
    }

    /**
     * Set return order item transferred id
     *
     * @param int $returnItemTransferredId
     * @return $this
     */
    public function setReturnItemTransferredId($returnItemTransferredId) {
        return $this->setData(self::RETURN_ITEM_TRANSFERRED_ID, $returnItemTransferredId);
    }

    /**
     * Get return order item id
     *
     * @return int
     */
    public function getReturnItemId() {
        return $this->_getData(self::RETURN_ITEM_ID);
    }

    /**
     * Set return order item id
     *
     * @param int $returnItemId
     * @return $this
     */
    public function setReturnItemId($returnItemId) {
        return $this->setData(self::RETURN_ITEM_ID, $returnItemId);
    }

    /**
     * Get created by
     *
     * @return string
     */
    public function getCreatedBy() {
        return $this->_getData(self::CREATED_BY);
    }

    /**
     * Set created by
     *
     * @param string|null $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy) {
        return $this->setData(self::CREATED_BY, $createdBy);
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