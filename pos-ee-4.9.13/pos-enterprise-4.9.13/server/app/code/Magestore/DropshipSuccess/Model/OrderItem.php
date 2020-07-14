<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model;

use Magestore\DropshipSuccess\Api\Data\OrderInterface;

/**
 * Class Batch
 * @package Magestore\DropshipSuccess\Model
 */
class OrderItem extends \Magento\Sales\Model\Order\Item
            implements \Magestore\DropshipSuccess\Api\Data\OrderItemInterface
{
    /**
     * get prepare ship
     *
     * @param
     * @return float
     */
    public function getQtyPrepareship()
    {
        return $this->_getData(self::QTY_PREPARESHIP);
    }

    /**
     * set prepare ship
     *
     * @param float $qty
     * @return OrderItemInterface
     */
    public function setQtyPrepareship($qty)
    {
        return $this->setData(self::QTY_PREPARESHIP, $qty);
    }

}
