<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Api\Staff;

interface RuleRepositoryInterface
{
    /**
     * @param string $aclResource
     * @param int $staffId
     * @return bool
     */
    public function isAllowPermission($aclResource, $staffId);
}