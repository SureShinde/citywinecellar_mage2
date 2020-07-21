<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class RedemptionLimitTypes implements ArrayInterface
{
    const NO_LIMIT = 0;
    const LIMIT_AMOUNT = 1;
    const LIMIT_PERCENT = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::NO_LIMIT,
                'label' => __('No')
            ],
            [
                'value' => self::LIMIT_AMOUNT,
                'label' => __('Yes (amount in reward points)')
            ],
            [
                'value' => self::LIMIT_PERCENT,
                'label' => __('Yes (percent of checkout sum)')
            ]
        ];

        return $options;
    }
}
