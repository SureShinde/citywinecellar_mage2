<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MeasurementUnit
 * @package Magestore\BarcodeSuccess\Model\Source
 */

class MeasurementUnit implements OptionSourceInterface
{

    const MM = 'mm';
    const CM = 'cn';
    const IN = 'in';
    const PX = 'px';
    const PERCENTAGE = '%';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = [
            self::MM => __('mm'),
            self::CM => __('cm'),
            self::IN => __('in'),
            self::PX => __('px'),
            self::PERCENTAGE => __('%')
        ];
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
