<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Locator;


interface BatchServiceInterface
{
    const CURRENT_BATCH_SESSION_ID = 'pick_request_current_batch_id';
    
    /**
     * Get currently Batch Id which user is working on
     * 
     * @return int
     */
    public function getCurrentBatchId();
    
}