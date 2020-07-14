<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;

use Magestore\Webpos\Api\Data\Config\LocationInterface;

/**
 * Class Location
 * @package Magestore\Webpos\Model\Config\Data
 */
class Location extends \Magento\Framework\DataObject implements LocationInterface
{
    /**
     * Get location id
     *
     * @return int
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set location id
     *
     * @param int $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * Get stock id
     *
     * @return int
     */
    public function getStockId()
    {
        return $this->getData(self::STOCK_ID);
    }

    /**
     * Set stock id
     *
     * @param int $stockId
     * @return LocationInterface
     */
    public function setStockId($stockId)
    {
        return $this->setData(self::STOCK_ID, $stockId);
    }
}