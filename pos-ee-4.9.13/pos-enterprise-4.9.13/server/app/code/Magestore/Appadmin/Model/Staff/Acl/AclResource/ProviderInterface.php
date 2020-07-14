<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Model\Staff\Acl\AclResource;

/**
 * Interface ProviderInterface
 * @package Magestore\Appadmin\Model\Staff\Acl\AclResource
 */
interface ProviderInterface
{
    /**
     * @return mixed
     */
    public function getAclResources();
}
