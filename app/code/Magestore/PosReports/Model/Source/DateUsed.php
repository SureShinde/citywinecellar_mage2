<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\PosReports\Model\Source;

/**
 * Class DateUsed
 *
 * Used to create Date Used
 */
class DateUsed implements \Magento\Framework\Data\OptionSourceInterface
{
    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::CREATED_AT => __("Order Created"),
            self::UPDATED_AT => __("Order Updated")
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
