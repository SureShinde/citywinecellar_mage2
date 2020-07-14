<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Sales\Order;

/**
 * Class ShipmentRepository
 *
 * @package Magestore\Webpos\Model\Sales\Order
 */
class ShipmentRepository implements \Magestore\Webpos\Api\Sales\Order\ShipmentRepositoryInterface
{
    /**
     * @var \Magento\Sales\Api\ShipOrderInterface
     */
    protected $shipOrder;

    /**
     * ShipmentRepository constructor.
     * @param \Magento\Sales\Api\ShipOrderInterface $shipOrder
     */
    public function __construct(
        \Magento\Sales\Api\ShipOrderInterface $shipOrder
    ) {
        $this->shipOrder = $shipOrder;
    }

    /**
     * @inheritdoc
     */
    public function createShipmentByOrderId($order_id)
    {
        return $this->shipOrder->execute($order_id);
    }
}
