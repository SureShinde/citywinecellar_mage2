<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PaymentOffline\Model\Source\Adminhtml;

/**
 * Class UsePayLater
 * @package Magestore\PaymentOffline\Model\Source\Adminhtml
 */
class UsePayLater implements \Magento\Framework\Option\ArrayInterface
{

    const YES = 1;
    const NO = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('No'), 'value' => self::NO],
            ['label' => __('Yes'), 'value' => self::YES]
        ];
    }
}
