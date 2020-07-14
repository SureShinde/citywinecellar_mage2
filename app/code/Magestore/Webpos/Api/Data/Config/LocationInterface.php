<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * Interface LocationInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface LocationInterface
{
    const LOCATION_ID = 'location_id';
    const STOCK_ID = 'stock_id';

    /**
     * Get location id
     *
     * @return int
     */
    public function getLocationId();

    /**
     * Set location id
     *
     * @param int $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId);

    /**
     * Get stock id
     *
     * @return int
     */
    public function getStockId();

    /**
     * Set stock id
     *
     * @param int $stockId
     * @return LocationInterface
     */
    public function setStockId($stockId);
}
