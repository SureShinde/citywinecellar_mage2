<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * Class Since
 * @package Magestore\Webpos\Model\Source\Adminhtml
 */
class Since implements \Magento\Framework\Option\ArrayInterface
{

    const SINCE_24H = '24h';
    const SINCE_7DAYS = '7days';
    const SINCE_MONTH = 'month';
    const SINCE_YTD = 'YTD';
    const SINCE_2YTD = '2YTD';
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Last 24 hours'), 'value' => self::SINCE_24H],
            ['label' => __('Last 7 days'), 'value' => self::SINCE_7DAYS],
            ['label' => __('Current month'), 'value' => self::SINCE_MONTH],
            ['label' => __('YTD'), 'value' => self::SINCE_YTD],
            ['label' => __('2YTD'), 'value' => self::SINCE_2YTD]
        ];
    }
}
