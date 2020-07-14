<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CategoryStatus
 */
class CategoryStatus implements ArrayInterface
{
    const STATUS_DISABLED = 0;

    const STATUS_ENABLED = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_DISABLED, 'label' => __('Disabled')],
            ['value' => self::STATUS_ENABLED, 'label' => __('Enabled')]
        ];
    }
}
