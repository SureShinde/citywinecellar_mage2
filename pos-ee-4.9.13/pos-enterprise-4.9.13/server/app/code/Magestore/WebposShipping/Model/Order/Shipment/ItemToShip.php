<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposShipping\Model\Order\Shipment;

use Magestore\WebposShipping\Api\Data\Order\Shipment\ItemToShipInterface;

class ItemToShip extends \Magento\Framework\Model\AbstractModel implements ItemToShipInterface
{
    /**
     * @return int|null
     */
    public function getOrderItemId()
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * @param int|null $orderItemId
     * @return ItemToShipInterface
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * @return float|null
     */
    public function getQtyToShip()
    {
        return $this->getData(self::QTY_TO_SHIP);
    }

    /**
     * @param float|null $qtyToShip
     * @return ItemToShipInterface
     */
    public function setQtyToShip($qtyToShip)
    {
        return $this->setData(self::QTY_TO_SHIP, $qtyToShip);
    }
}