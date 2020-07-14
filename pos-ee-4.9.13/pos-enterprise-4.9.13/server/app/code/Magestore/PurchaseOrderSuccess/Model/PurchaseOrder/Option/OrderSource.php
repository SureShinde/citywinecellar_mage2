<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

/**
 * Class OrderSource
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class OrderSource extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    /**
     * Purchase order status value
     */
    const SOURCE_NONE = ' ';
    
    const SOURCE_EMAIL = 1;
    
    const SOURCE_PHONE = 2;
    
    const SOURCE_FAX = 3;
    
    const SOURCE_VENDOR = 4;
    
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return [
            self::SOURCE_NONE => __('N/A'),
            self::SOURCE_EMAIL => __('Email'),
            self::SOURCE_PHONE => __('Phone'),
            self::SOURCE_FAX => __('Fax'),
            self::SOURCE_VENDOR => __('Vendor website'),
        ];
    }
}