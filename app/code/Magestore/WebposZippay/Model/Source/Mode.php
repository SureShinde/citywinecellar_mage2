<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposZippay\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 *
 *  Action Dropdown source
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
                'value' => 'https://merchantapi.zipmoney.com.au/v1',
                'label' => __('Production'),
            ],
            [
                'value' => "https://merchantapi.sandbox.zipmoney.com.au/v1",
                'label' => __('Sandbox')
            ]
        ];
    }
}