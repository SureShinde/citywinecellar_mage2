<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Sales\Order;

/**
 * Interface StatusRepositoryInterface
 * @package Magestore\Webpos\Api\Sales\Order
 */
interface StatusRepositoryInterface
{
    /**
     * Get Statuses
     *
     * @param
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getStatuses();
}
