<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout\Desktop;

/**
 * Class Mlist
 */
class Mlist extends \Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout
{
    /**
     * @return array
     */
    protected function getContentBlocks()
    {
        $result = parent::getContentBlocks();

        $result[] = [
            'value' => 'list',
            'label' => __("List"),
            'backend_image' => 'images/layout/assets/list_list.png',

        ];

        $result[] = [
            'value' => 'grid',
            'label' => __("Grid"),
            'backend_image' => 'images/layout/assets/list_grid.png',
        ];

        return $result;
    }
}
