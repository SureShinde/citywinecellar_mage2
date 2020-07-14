<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\AdjustStock\Options;

/**
 * Class Status
 * @package Magestore\AdjustStock\Model\AdjustStock\Options
 */
class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {
        $option = [];

        $option[] = ['label' => __('Pending'), 'value' => \Magestore\AdjustStock\Model\AdjustStock::STATUS_PENDING];
        $option[] = ['label' => __('Completed'), 'value' => \Magestore\AdjustStock\Model\AdjustStock::STATUS_COMPLETED];

        return $option;
    }

    /**
     * Return array of options as key-value pairs.
     *
     * @return array Format: array('<key>' => '<value>', '<key>' => '<value>', ...)
     */
    public function toOptionHash() {
        $option = [
            \Magestore\AdjustStock\Model\AdjustStock::STATUS_PENDING => __('Pending'),
            \Magestore\AdjustStock\Model\AdjustStock::STATUS_COMPLETED => __('Complete'),
        ];

        return $option;
    }
}
