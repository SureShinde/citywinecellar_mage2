<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api;

interface PermissionManagementInterface
{
    
    /* define permission resources */
    const VERIFY_ORDER_LIST = 'Magestore_OrderSuccess::need_to_verify';
    const VERIFY_ORDER = 'Magestore_OrderSuccess::verify_order';
    const VERIFY_ORDER_ALL_BATCH = 'Magestore_OrderSuccess::verify_order_all_batch';
    const PREPARE_SHIP_LIST = 'Magestore_OrderSuccess::need_to_ship';
    const PREPARE_SHIP = 'Magestore_OrderSuccess::prepare_ship';
    const PREPARE_SHIP_ALL_BATCH = 'Magestore_OrderSuccess::prepare_ship_all_batch';
    
    const ALL_ORDER_LIST = 'Magestore_OrderSuccess::allorder';
    const AWAITING_PAYMENT_LIST = 'Magestore_OrderSuccess::awaiting_payment';
    const BACK_ORDER_LIST = 'Magestore_OrderSuccess::backorder';
    const COMPLETED_ORDER_LIST = 'Magestore_OrderSuccess::completed';
    const CANCELED_ORDER_LIST = 'Magestore_OrderSuccess::canceled';
    const ONHOLD_ORDER_LIST = 'Magestore_OrderSuccess::hold';

    
    /**
     * @param $resourceId
     * @param null $user
     * @return bool
     */
    public function checkPermission($resourceId, $user = null);
}