<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api;

/**
 * Interface OrderItemRepositoryInterface
 * @package Magestore\DropshipSuccess\Api
 */
interface OrderItemRepositoryInterface extends \Magento\Sales\Api\OrderItemRepositoryInterface
{

    /**
     * update dropshi qty
     * 
     * @param  array $itemId
     * @param decimal $qty
     */
    public function updateDropshipQty($itemId, $qty);


}