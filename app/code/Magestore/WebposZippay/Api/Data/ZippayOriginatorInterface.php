<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Api\Data;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Api\Data
 */
interface ZippayOriginatorInterface
{
    const LOCATION_ID = 'locationId';
    const DEVICE_REF_CODE = 'deviceRefCode';
    const STAFF_ACTOR = 'staffActor';



    /**
     * @return string | float | null
     */
    public function getDeviceRefCode();

    /**
     * @param $device_ref_code
     * @return ZippayOriginatorInterface
     */
    public function setDeviceRefCode($device_ref_code);


    /**
     * @return string | float | null
     */
    public function getLocationId();

    /**
     * @param $location_id
     * @return ZippayOriginatorInterface
     */
    public function setLocationId($location_id);

    /**
     * @return \Magestore\WebposZippay\Api\Data\ZippayStaffActorInterface
     */
    public function getStaffActor();

    /**
     * @param $staff_actor
     * @return ZippayOriginatorInterface
     */
    public function setStaffActor($staff_actor);

}
