<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source;

/**
 * Giftvoucher Aftertax Model
 *
 * @author      Magestore Developer
 */
class AfterTax extends \Magento\Framework\DataObject
{
    /**
     * Get model option as array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            0 => 'Before tax',
            1 => 'After tax',
        ];
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
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
}
