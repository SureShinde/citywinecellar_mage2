<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class InventoryTransfer
 * @package Magestore\TransferStock\Model
 */
class InventoryTransfer extends AbstractModel implements \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterface
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->_init('Magestore\TransferStock\Model\ResourceModel\InventoryTransfer');
    }


    /**
     * @inheritdoc
     */
    public function getInventorytransferId() {
        return $this->getData(self::INVENTORYTRANSFER_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setInventorytransferId($inventorytransferId) {
        return $this->setData(self::INVENTORYTRANSFER_ID, $inventorytransferId);
    }

    /**
     * @inheritdoc
     */
    public function getInventorytransferCode() {
        return $this->getData(self::INVENTORYTRANSFER_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setInventorytransferCode($inventorytransferCode) {
        return $this->setData(self::INVENTORYTRANSFER_CODE, $inventorytransferCode);
    }

    /**
     * @inheritdoc
     */
    public function getSourceWarehouseCode() {
        return $this->getData(self::SOURCE_WAREHOUSE_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setSourceWarehouseCode($sourceWarehouseCode) {
        return $this->setData(self::SOURCE_WAREHOUSE_CODE, $sourceWarehouseCode);
    }

    /**
     * @inheritdoc
     */
    public function getDesWarehouseCode() {
        return $this->getData(self::DES_WAREHOUSE_CODE);
    }	
    /**
     * @inheritdoc
     */
    public function setDesWarehouseCode($desWarehouseCode) {
        return $this->setData(self::DES_WAREHOUSE_CODE, $desWarehouseCode);
    }

    /**
     * @inheritdoc
     */
    public function getReason() {
        return $this->getData(self::REASON);
    }	
    /**
     * @inheritdoc
     */
    public function setReason($reason) {
        return $this->setData(self::REASON, $reason);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedBy() {
        return $this->getData(self::CREATED_BY);
    }	
    /**
     * @inheritdoc
     */
    public function setCreatedBy($createdBy) {
        return $this->setData(self::CREATED_BY, $createdBy);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedOn() {
        return $this->getData(self::CREATED_ON);
    }	
    /**
     * @inheritdoc
     */
    public function setCreatedOn($createdOn) {
        return $this->setData(self::CREATED_ON, $createdOn);
    }

    /**
     * @inheritdoc
     */
    public function getStatus() {
        return $this->getData(self::STATUS);
    }	
    /**
     * @inheritdoc
     */
    public function setStatus($status) {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getStage() {
        return $this->getData(self::STAGE);
    }	
    /**
     * @inheritdoc
     */
    public function setStage($stage) {
        return $this->setData(self::STAGE, $stage);
    }

    /**
     * @inheritdoc
     */
    public function getQtyTransferred() {
        return $this->getData(self::QTY_TRANSFERRED);
    }	
    /**
     * @inheritdoc
     */
    public function setQtyTransferred($transferQty) {
        return $this->setData(self::QTY_TRANSFERRED, $transferQty);
    }

    /**
     * @inheritdoc
     */
    public function getQtyReceived() {
        return $this->getData(self::QTY_RECEIVED);
    }	
    /**
     * @inheritdoc
     */
    public function setQtyReceived($qtyReceived) {
        return $this->setData(self::QTY_RECEIVED, $qtyReceived);
    }
}
