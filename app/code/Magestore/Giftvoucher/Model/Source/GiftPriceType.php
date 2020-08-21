<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class GiftPriceType
 *
 * Source - Gift price type model
 */
class GiftPriceType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const GIFT_PRICE_TYPE_DEFAULT = 1;
    const GIFT_PRICE_TYPE_FIX = 2;
    const GIFT_PRICE_TYPE_PERCENT = 3;

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                [
                    'label' => __('Same as Gift Card Value'),
                    'value' => self::GIFT_PRICE_TYPE_DEFAULT
                ],
                [
                    'label' => __('Fixed Price'),
                    'value' => self::GIFT_PRICE_TYPE_FIX
                ],
                [
                    'label' => __('Percent of Gift Card value'),
                    'value' => self::GIFT_PRICE_TYPE_PERCENT
                ],
            ];
        }
        return $this->_options;
    }
}
