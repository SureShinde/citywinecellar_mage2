<?php

namespace Magestore\TransferStock\Api\Data\InventoryTransfer;

interface InventoryTransferInterface {
    const INVENTORYTRANSFER_ID = 'inventorytransfer_id';
    const INVENTORYTRANSFER_CODE = 'inventorytransfer_code';
    const SOURCE_WAREHOUSE_CODE = 'source_warehouse_code';
    const DES_WAREHOUSE_CODE = 'des_warehouse_code';
    const REASON = 'reason';
    const CREATED_BY = 'created_by';
    const CREATED_ON = 'created_on';
    const STATUS = 'status';
    const STAGE = 'stage';
    const QTY_TRANSFERRED = 'qty_transferred';
    const QTY_RECEIVED = 'qty_received';


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
     * Get Inventorytransfer Code
     *
     * @return string|null
     */
    public function getInventorytransferCode();	
    /**
     * Set Inventorytransfer Code
     *
     * @param string|null $inventorytransferCode
     * @return $this
     */
    public function setInventorytransferCode($inventorytransferCode);

    /**
     * Get Source Warehouse Code
     *
     * @return string|null
     */
    public function getSourceWarehouseCode();	
    /**
     * Set Source Warehouse Code
     *
     * @param string|null $sourceWarehouseCode
     * @return $this
     */
    public function setSourceWarehouseCode($sourceWarehouseCode);

    /**
     * Get Des Warehouse Code
     *
     * @return string|null
     */
    public function getDesWarehouseCode();	
    /**
     * Set Des Warehouse Code
     *
     * @param string|null $desWarehouseCode
     * @return $this
     */
    public function setDesWarehouseCode($desWarehouseCode);

    /**
     * Get Reason
     *
     * @return string|null
     */
    public function getReason();	
    /**
     * Set Reason
     *
     * @param string|null $reason
     * @return $this
     */
    public function setReason($reason);

    /**
     * Get Created By
     *
     * @return string|null
     */
    public function getCreatedBy();	
    /**
     * Set Created By
     *
     * @param string|null $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);

    /**
     * Get Created On
     *
     * @return string|null
     */
    public function getCreatedOn();	
    /**
     * Set Created On
     *
     * @param string|null $createdOn
     * @return $this
     */
    public function setCreatedOn($createdOn);

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus();	
    /**
     * Set Status
     *
     * @param string|null $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get Stage
     *
     * @return string|null
     */
    public function getStage();	
    /**
     * Set Stage
     *
     * @param string|null $stage
     * @return $this
     */
    public function setStage($stage);

    /**
     * Get Transfer Qty
     *
     * @return float|null
     */
    public function getQtyTransferred();
    /**
     * Set Transfer Qty
     *
     * @param float|null $transferQty
     * @return $this
     */
    public function setQtyTransferred($transferQty);

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