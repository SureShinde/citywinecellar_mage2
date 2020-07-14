<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Sales\Order\Item;

/**
 * Class Order
 * @package Magestore\Giftvoucher\Plugin\Sales\Order\Item
 */
class NamePlugin
{
    /**
     * @param \Magestore\Giftvoucher\Block\Adminhtml\Order\Item\Name $subject
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return array
     */
    public function beforeTruncateString(\Magestore\Giftvoucher\Block\Adminhtml\Order\Item\Name $subject, $value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        $length = 5000;

        return [$value, $length, $etc, $remainder, $breakWords];
    }
}
