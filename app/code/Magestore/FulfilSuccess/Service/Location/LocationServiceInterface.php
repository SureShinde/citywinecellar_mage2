<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Location;


interface LocationServiceInterface
{
    const CURRENT_WAREHOUSE_SESSION_ID = 'pick_request_current_warehouse_id';
    
    /**
     * Get currently Warehouse Id which user is working on
     * 
     * @return int
     */
    public function getCurrentWarehouseId();
    
    
    /**
     * Get Ids of allowed Warehouses which user can process pick requests
     * 
     * @param string $permissionResource
     * @return array
     */
    public function getAllowedWarehouses($permissionResource = null);
    
}