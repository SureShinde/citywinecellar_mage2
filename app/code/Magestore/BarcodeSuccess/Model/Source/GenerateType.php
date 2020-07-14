<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
/**
 * Class GenerateType
 * @package Magestore\BarcodeSuccess\Model\Source\GenerateType
 */

class GenerateType implements OptionSourceInterface
{

    const ITEM = 'item';
    const PURCHASE = 'purchase';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = [
            self::ITEM => __('Per Item'),
            self::PURCHASE => __('Per Purchase')
        ];
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
