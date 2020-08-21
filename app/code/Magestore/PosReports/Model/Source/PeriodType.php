<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\PosReports\Model\Source;

/**
 * Class PeriodType
 *
 * Used to create Period Type
 */
class PeriodType implements \Magento\Framework\Data\OptionSourceInterface
{
    const DAY = "day";
    const WEEK = "week";
    const MONTH = "month";
    const YEAR = "year";

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::DAY => __("Day"),
            self::WEEK => __("Week"),
            self::MONTH => __("Month"),
            self::YEAR => __("Year")
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
