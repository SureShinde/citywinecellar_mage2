<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\InventoryTransfer;

/**
 * Class ReceiveProduct
 * @package Magestore\TransferStock\Model\InventoryTransfer
 */
class ReceiveProduct extends \Magento\Framework\Model\AbstractModel implements \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveProductInterface
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct::class);
    }


    /**
     * @inheritdoc
     */
    public function getReceiveProductId() {
        return $this->getData(self::RECEIVE_PRODUCT_ID);
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
    public function getProductId() {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getProductName() {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritdoc
     */
    public function getProductSku() {
        return $this->getData(self::PRODUCT_SKU);
    }

    /**
     * @inheritdoc
     */
    public function getQty() {
        return $this->getData(self::QTY);
    }


    /**
     * @inheritdoc
     */
    public function setReceiveProductId($receiveProductId) {
        return $this->setData(self::RECEIVE_PRODUCT_ID, $receiveProductId);
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
    public function setProductId($productId) {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function setProductName($productName) {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    /**
     * @inheritdoc
     */
    public function setProductSku($productSku) {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }

    /**
     * @inheritdoc
     */
    public function setQty($qty) {
        return $this->setData(self::QTY, round($qty, 4));
    }
}
