<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

interface OrderItemRepositoryInterface
{
    /**
     * Mass Update PrepareShip Qty of Sales Items
     * 
     * @param array $items
     */
    public function massUpdatePrepareShipQty($items);
    
    /**
     * Retrieve order items collection
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getItemsCollection($pickRequestId);    
    
}