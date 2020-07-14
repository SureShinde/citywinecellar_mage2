<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\InventoryTransfer;

/**
 * Class Reveive
 * @package Magestore\TransferStock\Model\InventoryTransfer
 */
class Receive extends \Magento\Framework\Model\AbstractModel implements \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterface
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->_init(\Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive::class);
    }


    /**
     * @inheritdoc
     */
    public function getReceiveId() {
        return $this->getData(self::RECEIVE_ID);
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
    public function getCreatedBy() {
        return $this->getData(self::CREATED_BY);
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
    public function getTotalQty() {
        return $this->getData(self::TOTAL_QTY);
    }


    /**
     * @inheritdoc
     */
    public function setReceiveId($receiveId) {
        return $this->setData(self::RECEIVE_ID, $receiveId);
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
    public function setCreatedBy($createdBy) {
        return $this->setData(self::CREATED_BY, $createdBy);
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
    public function setTotalQty($totalQty) {
        return $this->setData(self::TOTAL_QTY, $totalQty);
    }
}
