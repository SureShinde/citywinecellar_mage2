<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PackRequest\Warehouse;

use Magestore\FulfilSuccess\Api\PermissionManagementInterface;
use Magestore\FulfilSuccess\Model\Warehouse\Options as WarehouseOptions;

class Options extends WarehouseOptions
{
    /**
     * Get permission of current task
     * 
     * @return string
     */
    public function getPermission()
    {
        return PermissionManagementInterface::PACK_ITEM;
    }

}