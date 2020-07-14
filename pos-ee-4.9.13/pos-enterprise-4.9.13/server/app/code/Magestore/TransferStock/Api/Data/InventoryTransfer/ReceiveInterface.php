<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Api\Data\InventoryTransfer;

/**
 * Interface ReceiveInterface
 * @package Magestore\TransferStock\Api\Data\InventoryTransfer
 */
interface ReceiveInterface {
    const RECEIVE_ID = 'receive_id';
    const INVENTORYTRANSFER_ID = 'inventorytransfer_id';
    const CREATED_BY = 'created_by';
    const CREATED_ON = 'created_on';
    const TOTAL_QTY = 'total_qty';


    /**
     * Get Receive Id
     *
     * @return int|null
     */
    public function getReceiveId();

    /**
     * Get Inventorytransfer Id
     *
     * @return int|null
     */
    public function getInventorytransferId();

    /**
     * Get Created By
     *
     * @return string|null
     */
    public function getCreatedBy();

    /**
     * Get Created On
     *
     * @return string|null
     */
    public function getCreatedOn();

    /**
     * Get Total Qty
     *
     * @return float|null
     */
    public function getTotalQty();



    /**
     * Set Receive Id
     *
     * @param int|null $receiveId
     * @return $this
     */
    public function setReceiveId($receiveId);

    /**
     * Set Inventorytransfer Id
     *
     * @param int|null $inventorytransferId
     * @return $this
     */
    public function setInventorytransferId($inventorytransferId);

    /**
     * Set Created By
     *
     * @param string|null $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);

    /**
     * Set Created On
     *
     * @param string|null $createdOn
     * @return $this
     */
    public function setCreatedOn($createdOn);

    /**
     * Set Total Qty
     *
     * @param float|null $totalQty
     * @return $this
     */
    public function setTotalQty($totalQty);
}
