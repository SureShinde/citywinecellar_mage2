<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Class SupplierWithNone
 * @package Magestore\ReportSuccess\Model\Source\Adminhtml
 */
class SupplierWithNone extends Supplier
{
    const NONE_VALUE = 'none-supplier';
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = array('value' => self::NONE_VALUE, 'label' => __(' '));
        $options = array_merge($options, parent::toOptionArray());
        return $options;
    }

}
