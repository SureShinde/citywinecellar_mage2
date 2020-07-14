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
interface ZippayStaffActorInterface
{
    const REF_CODE = 'refCode';


    /**
     * @return string | float | null
     */
    public function getRefCode();

    /**
     * @param $ref_code
     * @return ZippayStaffActorInterface
     */
    public function setRefCode($ref_code);

}
