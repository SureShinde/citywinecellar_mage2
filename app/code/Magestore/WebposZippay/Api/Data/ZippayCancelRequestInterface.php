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
interface ZippayCancelRequestInterface
{
    const REF_CODE = 'refCode';
    const ORIGINATOR = 'originator';


    /**
     * @return string | float | null
     */
    public function getRefCode();

    /**
     * @param $ref_code
     * @return ZippayCancelRequestInterface
     */
    public function setRefCode($ref_code);

    /**
     * @return \Magestore\WebposZippay\Api\Data\ZippayOriginatorInterface
     */
    public function getOriginator();

    /**
     * @param $originator
     * @return ZippayCancelRequestInterface
     */
    public function setOriginator($originator);

}
