<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\Data;

/**
 * Interface OrderItemInterface
 * @package Magestore\DropshipSuccess\Api\Data
 */
interface OrderItemInterface extends \Magento\Sales\Api\Data\OrderItemInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const QTY_PREPARESHIP = 'qty_prepareship';

    /**
     * get qty prepare ship
     *
     * @return float
     */
    public function getQtyPrepareship();

    /**
     * set qty prepare ship
     *
     * @param float $qty
     * @return OrderItemInterface
     */
    public function setQtyPrepareship($qty);

}