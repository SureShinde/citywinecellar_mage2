<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class Designpattern
 *
 * Source - Design pattern model
 */
class DesignPattern extends \Magento\Framework\DataObject
{
    const PATTERN_LEFT = 1;
    const PATTERN_TOP = 2;
    const PATTERN_CENTER = 3;

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::PATTERN_LEFT => __('Left'),
            self::PATTERN_TOP => __('Top'),
            self::PATTERN_CENTER => __('Center'),
        ];
    }

    /**
     * Get Options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        foreach ($this->getOptionArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
