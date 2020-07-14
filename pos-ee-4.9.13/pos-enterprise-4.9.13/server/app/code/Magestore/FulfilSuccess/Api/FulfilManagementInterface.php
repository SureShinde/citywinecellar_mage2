<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

/**
 * Interface FulfilManagementInterface
 * @package Magestore\FulfilSuccess\Api
 */
interface FulfilManagementInterface
{
    /**
     * @return boolean
     */
    public function isMSIEnable();

    /**
     * @return boolean
     */
    public function isInventorySuccessEnable();
}
