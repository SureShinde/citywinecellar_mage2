<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * Source option SelectorShipping
 */
class SelectorShipping implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('All Allowed Shipping')],
            ['value' => 1, 'label' => __('Specific Shipping')],
        ];
    }
}
