<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Model\Action;

/**
 * Class Status
 * @package Magestore\FulfilSuccess\Model\PackRequest\Options
 */
class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    const PICKED = 1;
    const PACKED = 2;

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray() {

            return [
                self::PICKED => __('Picked'),
                self::PACKED => __('Packed')
            ];

    }
}
