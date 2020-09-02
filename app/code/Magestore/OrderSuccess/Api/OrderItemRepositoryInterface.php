<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api;

/**
 * Interface OrderItemRepositoryInterface
 * @package Magestore\OrderSuccess\Api
 */
interface OrderItemRepositoryInterface extends \Magento\Sales\Api\OrderItemRepositoryInterface
{

    /**
     * Move item to back order
     * 
     * @param  array $itemId
     * @param decimal $qty
     */
    public function moveItemToBackOrder($itemId, $qty);

    /**
     * Remove items from back order
     *
     * @param  int orderId
     */
    public function removeFromBackOrder($orderId);

}