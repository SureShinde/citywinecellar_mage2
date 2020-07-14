<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel;

/**
 * Class PurchaseOrderCode
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel
 */
class PurchaseOrderCode extends AbstractResource
{
    const TABLE_PURCHASE_ORDER_CODE = 'os_purchase_order_code';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_CODE, 'purchase_order_code_id');
    }
}