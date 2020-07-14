<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class
 */
class PostStatus implements ArrayInterface
{
    const STATUS_DISABLED = 0;

    const STATUS_HIDDEN = 1;

    const STATUS_ENABLED = 2;

    const STATUS_SCHEDULED = 3;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_DISABLED, 'label' => __('Disabled')],
            ['value' => self::STATUS_HIDDEN, 'label' => __('Hidden')],
            ['value' => self::STATUS_ENABLED, 'label' => __('Published')],
            ['value' => self::STATUS_SCHEDULED, 'label' => __('Scheduled')]
        ];
    }
}
