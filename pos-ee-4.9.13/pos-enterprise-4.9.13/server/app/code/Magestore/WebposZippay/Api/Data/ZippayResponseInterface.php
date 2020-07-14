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
interface ZippayResponseInterface
{
    const ERROR = 'error';

    /**
     * @return \Magestore\WebposZippay\Api\Data\ZippayErrorInterface|null
     */
    public function getError();

    /**
     * @param $error
     * @return ZippayPurchaseResponseInterface
     */
    public function setError($error);
}
