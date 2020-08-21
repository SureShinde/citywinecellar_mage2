<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\PosReports\Model\Source;

/**
 * Class FirstDayOfWeek
 *
 * Used to create First Day Of Week
 */
class FirstDayOfWeek implements \Magento\Framework\Data\OptionSourceInterface
{
    const SUNDAY = 0;
    const MONDAY = 1;

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::SUNDAY => __("Sunday"),
            self::MONDAY => __("Monday")
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
