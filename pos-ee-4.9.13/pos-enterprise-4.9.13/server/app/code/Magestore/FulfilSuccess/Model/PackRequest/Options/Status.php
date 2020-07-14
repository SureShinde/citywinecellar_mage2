<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\PackRequest\Options;

/**
 * Class Status
 * @package Magestore\FulfilSuccess\Model\PackRequest\Options
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

        $option[] = ['label' => __('Pending'), 'value' => \Magestore\FulfilSuccess\Api\Data\PackRequestInterface::STATUS_PACKING];
        $option[] = ['label' => __('Partially Packed'), 'value' => \Magestore\FulfilSuccess\Api\Data\PackRequestInterface::STATUS_PARTIAL_PACK];
        $option[] = ['label' => __('Completed'), 'value' => \Magestore\FulfilSuccess\Api\Data\PackRequestInterface::STATUS_PACKED];
        $option[] = ['label' => __('Canceled'), 'value' => \Magestore\FulfilSuccess\Api\Data\PackRequestInterface::STATUS_CANCELED];

        return $option;
    }

    /**
     * Return array of options as key-value pairs.
     *
     * @return array Format: array('<key>' => '<value>', '<key>' => '<value>', ...)
     */
    public function toOptionHash() {
        $options = [];
        foreach (self::toOptionArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
