<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposTyro\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 *
 * Authorize.net Payment Action Dropdown source
 */
class Mode implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'live',
                'label' => __('Live'),
            ],
            [
                'value' => "test",
                'label' => __('Test')
            ],
            [
                'value' => "simulator",
                'label' => __('Simulator')
            ]
        ];
    }
}