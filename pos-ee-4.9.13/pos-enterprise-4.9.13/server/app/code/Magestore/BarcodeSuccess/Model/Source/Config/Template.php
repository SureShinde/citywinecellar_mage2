<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Source\Config;

use Magestore\BarcodeSuccess\Model\Source\Template as TemplateSource;

/**
 * Class Template
 * @package Magestore\BarcodeSuccess\Model\Source\Template
 */

class Template extends TemplateSource
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'label' => __('-- Select Template --'),
            'value' => "0"
        ];
        $parentOptions = parent::toOptionArray();
        $options = array_merge($options, $parentOptions);
        return $options;
    }
}
