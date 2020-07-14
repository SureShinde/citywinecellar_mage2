<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Config\Source;

/**
 * Class ScheduleTime
 * @package Magestore\ReportSuccess\Model\Config\Source
 */
class ScheduleTime implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        for ($hour = 0; $hour < 24; $hour++) {
            $hourText = $hour<10?'0'.$hour:$hour;
            $result[] = $hourText.':00';
        }

        return $result;
    }

}
