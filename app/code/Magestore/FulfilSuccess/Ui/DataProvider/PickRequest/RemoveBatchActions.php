<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\PickRequest;

class RemoveBatchActions extends BatchActions
{
    /**
     * @var string
     */
    protected $urlPath = 'fulfilsuccess/pickRequest/removeFromBatch';
    
    /**
     * @var type 
     */
    protected $subActionType = 'remove';        
    
}