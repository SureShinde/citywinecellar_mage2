<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout\Mobile;

/**
 * Class
 */
class Post extends \Amasty\Blog\Block\Adminhtml\System\Config\Field\Layout\Mobile
{
    /**
     * @return array
     */
    protected function getContentBlocks()
    {
        $result = parent::getContentBlocks();

        $result[] = [
            'value' => 'post',
            'label' => __("Post"),
            'backend_image' => 'images/layout/assets/post.png',
        ];

        return $result;
    }
}
