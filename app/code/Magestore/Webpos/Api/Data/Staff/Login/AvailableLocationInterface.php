<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Staff\Login;

/**
 * @api
 */
/**
 * Interface LoginResultInterface
 * @package Magestore\Webpos\Api\Data\Staff\Login
 */
interface AvailableLocationInterface
{
    const LOCATIONS = 'locations';

    /**
     * @return \Magestore\Webpos\Api\Data\Staff\Login\LocationResultInterface[]
     */
    public function getLocations();

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\Login\LocationResultInterface[] $locations
     * @return AvailableLocationInterface
     */
    public function setLocations($locations);
}