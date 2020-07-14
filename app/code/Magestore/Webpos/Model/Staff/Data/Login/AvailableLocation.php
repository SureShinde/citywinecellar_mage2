<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff\Data\Login;

/**
 * Class LocationResult
 * @package Magestore\Webpos\Model\Staff\Data\Login
 */
class AvailableLocation extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Staff\Login\AvailableLocationInterface
{
    /**
     * @inheritdoc
     */
    public function getLocations(){
        return $this->getData(self::LOCATIONS);
    }

    /**
     * @inheritdoc
     */
    public function setLocations($locations){
        return $this->setData(self::LOCATIONS, $locations);
    }
}