<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * Source SelectorPayment
 */
class SelectorPayment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('All Allowed Payments')],
            ['value' => 1, 'label' => __('Specific Payments')],
        ];
    }
}
