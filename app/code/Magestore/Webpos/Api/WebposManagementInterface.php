<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api;

/**
 * Interface WebposManagementInterface
 * @package Magestore\Webpos\Api
 */
interface WebposManagementInterface
{
    /**
     * @return boolean
     */
    public function isMSIEnable();

    /**
     * @return boolean
     */
    public function isWebposStandard();
}
