<?php

namespace Magestore\TransferStock\Model\InventoryTransfer;

class InventoryTransferProduct extends \Magento\Framework\Model\AbstractModel implements \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterface  {

    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId() {
        return $this->getData(self::ENTITY_ID);
    }	
    /**
     * @inheritdoc
     */
    public function setEntityId($entityId) {
        return $this->setData(self::ENTITY_ID, $entityId);
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
    public function getProductId() {
        return $this->getData(self::PRODUCT_ID);
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
    public function getProductName() {
        return $this->getData(self::PRODUCT_NAME);
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
    public function getProductSku() {
        return $this->getData(self::PRODUCT_SKU);
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
    public function getBarcode() {
        return $this->getData(self::BARCODE);
    }	
    /**
     * @inheritdoc
     */
    public function setBarcode($barcode) {
        return $this->setData(self::BARCODE, $barcode);
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
    public function setQtyTransferred($qtyTransferred) {
        return $this->setData(self::QTY_TRANSFERRED, $qtyTransferred);
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