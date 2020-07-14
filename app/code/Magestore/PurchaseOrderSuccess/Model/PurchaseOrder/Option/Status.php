<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

/**
 * Class Status
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class Status extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    /**
     * Purchase order status value
     */
    const STATUS_PENDING = 1;
    
    const STATUS_COMFIRMED = 2;
    
    const STATUS_PROCESSING = 3;
    
    const STATUS_COMPLETED = 4;
    
    const STATUS_CANCELED = 5;
    
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return [
            self::STATUS_PENDING => __('Pending'), 
            self::STATUS_COMFIRMED => __('Confirmed'),
            self::STATUS_PROCESSING => __('Processing'),
            self::STATUS_COMPLETED => __('Completed'),
            self::STATUS_CANCELED => __('Canceled')
        ];
    }
}