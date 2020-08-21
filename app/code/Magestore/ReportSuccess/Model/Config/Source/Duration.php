<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Config\Source;

/**
 * Duration config model
 */
class Duration implements \Magento\Framework\Option\ArrayInterface
{

    const LAST_7_DAYS = 'last_7_days';

    const LAST_30_DAYS = 'last_30_days';

    const LAST_3_MONTHS = 'last_3_months';

    const LAST_6_MONTHS = 'last_6_months';

    const LAST_12_MONTHS = 'last_12_months';

    const LIFE_TIME = 'life_time';

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {

        $durationArrays = [];

        $durationArrays[0] = [
            'label' => 'Last 7 days',
            'value' => self::LAST_7_DAYS
        ];
        $durationArrays[1] = [
            'label' => 'Last 30 days',
            'value' => self::LAST_30_DAYS
        ];
        $durationArrays[2] = [
            'label' => 'Last 3 months',
            'value' => self::LAST_3_MONTHS
        ];
        $durationArrays[3] = [
            'label' => 'Last 6 months',
            'value' => self::LAST_6_MONTHS
        ];
        $durationArrays[4] = [
            'label' => 'Last 12 months',
            'value' => self::LAST_12_MONTHS
        ];

        $durationArrays[5] = [
            'label' => 'Lifetime',
            'value' => self::LIFE_TIME
        ];
        return $durationArrays;
    }
}
