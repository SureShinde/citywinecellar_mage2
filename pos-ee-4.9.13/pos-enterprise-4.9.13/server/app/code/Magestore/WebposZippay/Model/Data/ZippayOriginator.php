<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Model\Data;
use Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Model\Data
 */
class ZippayOriginator extends \Magento\Framework\DataObject implements ZippayOriginatorInterface
{


    public function getLocationId()
    {
        return $this->_getData(self::LOCATION_ID);
    }

    public function setLocationId($location_id)
    {
        return $this->setData(self::LOCATION_ID, $location_id);
    }

    public function getDeviceRefCode()
    {
        return $this->_getData(self::DEVICE_REF_CODE);
    }

    public function setDeviceRefCode($device_ref_code)
    {
        return $this->setData(self::DEVICE_REF_CODE, $device_ref_code);
    }

    public function getStaffActor()
    {
        return $this->_getData(self::STAFF_ACTOR);
    }

    public function setStaffActor($staff_actor)
    {
        return $this->setData(self::STAFF_ACTOR, $staff_actor);
    }


}
