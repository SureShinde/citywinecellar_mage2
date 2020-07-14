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

class Status implements OptionSourceInterface
{

    const ACTIVE = '1';
    const INACTIVE = '2';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = [
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive')
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
