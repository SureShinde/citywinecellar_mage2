<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

interface PackRequestOrderItemRepositoryInterface
{
    /**
     * Get need-to-pack items collection
     *
     * @param int $packRequestId
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getNeedToPackItemsCollection($packRequestId);

    /**
     * @param int $packRequestId
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getPackedItemsCollection($packRequestId);
}