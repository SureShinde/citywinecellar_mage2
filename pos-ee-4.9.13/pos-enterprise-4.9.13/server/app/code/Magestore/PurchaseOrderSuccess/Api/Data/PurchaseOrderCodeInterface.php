<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\Data;

interface PurchaseOrderCodeInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PURCHASE_ORDER_CODE_ID = 'purchase_order_code_id';

    const CODE = 'code';

    const CURRENT_ID = 'current_id';

    /**#@-*/

    /**
     * Get purchase order code id
     *
     * @return int
     */
    public function getPurchaseOrderCodeId();

    /**
     * Set purchase order code id
     *
     * @param int $purchaseOrderCodeId
     * @return $this
     */
    public function setPurchaseOrderCodeId($purchaseOrderCodeId);

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get current id
     *
     * @return int
     */
    public function getCurrentId();

    /**
     * Set current id
     *
     * @param int $currentId
     * @return $this
     */
    public function setCurrentId($currentId);
}