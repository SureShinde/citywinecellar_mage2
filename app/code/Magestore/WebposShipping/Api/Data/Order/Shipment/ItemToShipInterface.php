<?php

namespace Magestore\WebposShipping\Api\Data\Order\Shipment;

interface ItemToShipInterface
{
    const ORDER_ITEM_ID = 'order_item_id';
    const QTY_TO_SHIP = 'qty_to_ship';

    /**
     * @return int|null
     */
    public function getOrderItemId();

    /**
     * @param int|null $orderItemId
     * @return ItemToShipInterface
     */
    public function setOrderItemId($orderItemId);

    /**
     * @return float|null
     */
    public function getQtyToShip();

    /**
     * @param float|null $qtyToShip
     * @return ItemToShipInterface
     */
    public function setQtyToShip($qtyToShip);
}