<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

interface PermissionManagementInterface
{
    
    /* define permission resources */
    const PICK_ITEM = 'Magestore_FulfilSuccess::pick_request';
    const PICK_MOVE_TO_NEED_SHIP = 'Magestore_FulfilSuccess::pick_move_to_need_ship';
    const PICK_ALL_BATCH = 'Magestore_FulfilSuccess::pick_request_all_batch';
    
    const PACK_ITEM = 'Magestore_FulfilSuccess::pack_request';
    const DELIVERY_PACKAGE = 'Magestore_FulfilSuccess::delivery_package';

    
    /**
     * @param $resourceId
     * @param null $user
     * @return bool
     */
    public function checkPermission($resourceId, $user = null);
}