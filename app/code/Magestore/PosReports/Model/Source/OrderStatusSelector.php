<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\PosReports\Model\Source;

/**
 * Class OrderStatusSelector
 *
 * Used to create Order Status Selector
 */
class OrderStatusSelector implements \Magento\Framework\Data\OptionSourceInterface
{
    const ANY = "";
    const SPECIFIED = 1;

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::ANY => __("Any"),
            self::SPECIFIED => __("Specified")
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
