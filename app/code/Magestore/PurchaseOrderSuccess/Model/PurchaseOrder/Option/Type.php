<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option;

/**
 * Class Type
 * @package Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option
 */
class Type extends \Magestore\PurchaseOrderSuccess\Model\Option\AbstractOption
{
    /**
     * Purchase order type value
     */
    const TYPE_QUOTATION = 1;
    
    const TYPE_PURCHASE_ORDER = 2;
    
    const TYPE_QUOTATION_LABEL = 'Quotation';
    
    const TYPE_PURCHASE_ORDER_LABEL = 'Purchase Order';
    
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionHash()
    {
        return [
            self::TYPE_QUOTATION => __(self::TYPE_QUOTATION_LABEL), 
            self::TYPE_PURCHASE_ORDER => __(self::TYPE_PURCHASE_ORDER_LABEL)
        ];
    }
    
    public static function getTypeLabel($type){
        switch ($type){
            case self::TYPE_QUOTATION:
                return __(self::TYPE_QUOTATION_LABEL)->__toString();
            case self::TYPE_PURCHASE_ORDER:
                return __(self::TYPE_PURCHASE_ORDER_LABEL)->__toString();
        }
    }

}