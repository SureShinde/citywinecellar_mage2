<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source\GiftTemplate;

/**
 * Class StatusOptions
 *
 * Gift template status options model
 */
class StatusOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    /**
     * Get the gift card's type
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::STATUS_ENABLE => __('Enable'),
            self::STATUS_DISABLE => __('Disable'),
        ];
    }

    /**
     * Get All Options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if ($this->_options === null) {
            $this->_options = [];
            foreach ($this->getOptionArray() as $value => $label) {
                $this->_options[] = [
                    'label' => $label,
                    'value' => $value
                ];
            }
        }
        if ($withEmpty) {
            array_unshift($this->_options, [
                'value' => '',
                'label' => __('-- Please Select --'),
            ]);
        }
        return $this->_options;
    }
}
