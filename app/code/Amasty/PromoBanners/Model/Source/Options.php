<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Model\Source;

class Options implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => __('Show all, sorted by priority')),
            array('value' => 1, 'label' => __('Show only one with the highest priority')),
        );
    }
}
