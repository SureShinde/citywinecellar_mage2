<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Colors
 */
class Colors implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '-classic', 'label' => __('Classic')],
            ['value' => '-red', 'label' => __('Red')],
            ['value' => '-green', 'label' => __('Green')],
            ['value' => '-blue', 'label' => __('Blue')],
            ['value' => '-grey', 'label' => __('Grey')],
            ['value' => '-purple', 'label' => __('Purple')],
        ];
    }
}
