<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\PosReports\Model\Source;

/**
 * Class TimeRange
 *
 * Used to create Time Range
 */
class TimeRange implements \Magento\Framework\Data\OptionSourceInterface
{
    const TODAY = "today";
    const YESTERDAY = "yesterday";
    const LAST_7_DAYS = "last_7_days";
    const LAST_30_DAYS = "last_30_days";
    const THIS_YEAR = "this_year";
    const LAST_YEAR = "last_year";
    const CUSTOM_RANGE = "";

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::TODAY => __("Today"),
            self::YESTERDAY => __("Yesterday"),
            self::LAST_7_DAYS => __("Last 7 days"),
            self::LAST_30_DAYS => __("Last 30 days"),
            self::THIS_YEAR => __("This year"),
            self::LAST_YEAR => __("Last year"),
            self::CUSTOM_RANGE => __("Custom range")
        ];
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionsData = $this->getOptionArray();
        $options = [];
        foreach ($optionsData as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
