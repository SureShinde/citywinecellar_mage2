<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder;

/**
 * Class History
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder
 */
class History extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\AbstractResource
{
    const TABLE_PURCHASE_ORDER_HISTORY = 'os_purchase_order_history';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_HISTORY, 'purchase_order_history_id');
    }
}