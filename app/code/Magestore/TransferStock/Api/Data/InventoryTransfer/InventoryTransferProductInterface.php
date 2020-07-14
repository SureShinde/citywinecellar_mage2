<?php

namespace Magestore\TransferStock\Api\Data\InventoryTransfer;

interface InventoryTransferProductInterface {
    const ENTITY_ID = 'entity_id';
    const INVENTORYTRANSFER_ID = 'inventorytransfer_id';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_SKU = 'product_sku';
    const BARCODE = 'barcode';
    const QTY_TRANSFERRED = 'qty_transferred';
    const QTY_RECEIVED = 'qty_received';


    /**
     * Get Entity Id
     *
     * @return int|null
     */
    public function getEntityId();	
    /**
     * Set Entity Id
     *
     * @param int|null $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get Inventorytransfer Id
     *
     * @return int|null
     */
    public function getInventorytransferId();	
    /**
     * Set Inventorytransfer Id
     *
     * @param int|null $inventorytransferId
     * @return $this
     */
    public function setInventorytransferId($inventorytransferId);

    /**
     * Get Product Id
     *
     * @return int|null
     */
    public function getProductId();	
    /**
     * Set Product Id
     *
     * @param int|null $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get Product Name
     *
     * @return string|null
     */
    public function getProductName();	
    /**
     * Set Product Name
     *
     * @param string|null $productName
     * @return $this
     */
    public function setProductName($productName);

    /**
     * Get Product Sku
     *
     * @return string|null
     */
    public function getProductSku();	
    /**
     * Set Product Sku
     *
     * @param string|null $productSku
     * @return $this
     */
    public function setProductSku($productSku);

    /**
     * Get Barcode
     *
     * @return string|null
     */
    public function getBarcode();	
    /**
     * Set Barcode
     *
     * @param string|null $barcode
     * @return $this
     */
    public function setBarcode($barcode);

    /**
     * Get Qty Transferred
     *
     * @return float|null
     */
    public function getQtyTransferred();	
    /**
     * Set Qty Transferred
     *
     * @param float|null $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred);

    /**
     * Get Qty Received
     *
     * @return float|null
     */
    public function getQtyReceived();	
    /**
     * Set Qty Received
     *
     * @param float|null $qtyReceived
     * @return $this
     */
    public function setQtyReceived($qtyReceived);
}