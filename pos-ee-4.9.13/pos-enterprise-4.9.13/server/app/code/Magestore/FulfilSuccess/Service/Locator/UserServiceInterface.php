<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Locator;


interface UserServiceInterface
{
    /**
     * Get currently User Id
     * 
     * @return int
     */
    public function getCurrentUserId();
    
}