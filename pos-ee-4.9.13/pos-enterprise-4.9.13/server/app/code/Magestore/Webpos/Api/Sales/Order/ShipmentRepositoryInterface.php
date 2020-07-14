<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Sales\Order;

/**
 * Interface ShipmentRepositoryInterface
 * @package Magestore\Webpos\Api\Sales\Order
 */
interface ShipmentRepositoryInterface
{
    /**
     * Create shipment by order id
     *
     * @param int $order_id
     * @return int
     */
    public function createShipmentByOrderId($order_id);
}
