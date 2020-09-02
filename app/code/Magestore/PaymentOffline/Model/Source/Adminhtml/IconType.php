<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PaymentOffline\Model\Source\Adminhtml;

/**
 * Class IconType
 * @package Magestore\PaymentOffline\Model\Source\Adminhtml
 */
class IconType implements \Magento\Framework\Option\ArrayInterface
{

    const USE_SUGGEST = 1;
    const USE_CUSTOM = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Use the Suggested Icon'), 'value' => self::USE_SUGGEST],
            ['label' => __('Use the Custom Icon'), 'value' => self::USE_CUSTOM]
        ];
    }
}
