<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface ReturnOrderItemTransferredInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */

    const RETURN_ITEM_TRANSFERRED_ID = 'return_item_transferred_id';

    const RETURN_ITEM_ID = 'return_item_id';

    const QTY_TRANSFERRED = 'qty_transferred';

    const WAREHOUSE_ID = 'warehouse_id';

    const TRANSFERRED_AT = 'transferred_at';

    const CREATED_AT = 'created_at';

    const CREATED_BY = 'created_by';

    /**#@-*/

    /**
     * Get return order item transferred id
     *
     * @return int
     */
    public function getReturnItemTransferredId();

    /**
     * Set return order item transferred id
     *
     * @param int $returnItemTransferredId
     * @return $this
     */
    public function setReturnItemTransferredId($returnItemTransferredId);

    /**
     * Get return order item id
     *
     * @return int
     */
    public function getReturnItemId();

    /**
     * Set return order item id
     *
     * @param int $returnItemId
     * @return $this
     */
    public function setReturnItemId($returnItemId);

    /**
     * Get qty transferred
     *
     * @return float
     */
    public function getQtyTransferred();

    /**
     * Set qty transferred
     *
     * @param float $qtyTransferred
     * @return $this
     */
    public function setQtyTransferred($qtyTransferred);

    /**
     * Get warehouse id
     *
     * @return int
     */
    public function getWarehouseId();

    /**
     * Set warehouse id
     *
     * @param float $warehouseId
     * @return $this
     */
    public function setWarehouseId($warehouseId);

    /**
     * Get transferred at
     *
     * @return string
     */
    public function getTransferredAt();

    /**
     * Set transferred at
     *
     * @param string $transferredAt
     * @return $this
     */
    public function setTransferredAt($transferredAt);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedBy();

    /**
     * Set created at
     *
     * @param string|null $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);
}