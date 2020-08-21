<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source;

/**
 * Source options CustomDiscountType
 */
class CustomDiscountType implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_FIXED = "$";
    const TYPE_PERCENT = "%";

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => self::TYPE_FIXED, 'label' => __("Fixed")];
        $options[] = ['value' => self::TYPE_PERCENT, 'label' => __("Percent")];
        return $options;
    }
}
