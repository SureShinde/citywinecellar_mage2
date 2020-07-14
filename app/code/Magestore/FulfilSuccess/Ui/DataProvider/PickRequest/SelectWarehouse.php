<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\PickRequest;

use Magestore\FulfilSuccess\Ui\DataProvider\FulfilRequest\SelectWarehouse as FulfilSelectWarehouse;

class SelectWarehouse extends FulfilSelectWarehouse
{

    /**
     * Get action url path
     * 
     * @return string
     */    
    public function getUrlPath()
    {
        return 'fulfilsuccess/pickrequest/changeWarehouse';
    }

}
