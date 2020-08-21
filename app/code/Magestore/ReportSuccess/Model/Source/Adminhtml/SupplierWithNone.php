<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Supplier with none model
 */
class SupplierWithNone extends Supplier
{
    const NONE_VALUE = 'none-supplier';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => self::NONE_VALUE, 'label' => __(' ')];
        $options = array_merge($options, parent::toOptionArray());
        return $options;
    }
}
