<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Config\Source;

/**
 * Schedule time config model
 */
class ScheduleTime implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourText = $hour<10?'0'.$hour:$hour;
            $result[] = $hourText.':00';
        }

        return $result;
    }
}
