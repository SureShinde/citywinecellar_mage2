<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout;

/**
 * Class Mobile
 */
class Mobile extends \Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout
{
    /**
     * @return array
     */
    protected function getLayouts()
    {
        $config = [
            ['value' => 'two-columns-left', 'label' => __("Two Columns and Left Sidebar")],
            ['value' => 'two-columns-right', 'label' => __("Two Columns and Right Sidebar")],
        ];

        return $config;
    }
}
