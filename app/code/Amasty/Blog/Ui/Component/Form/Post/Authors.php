<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Ui\Component\Form\Post;

class Authors extends \Amasty\Blog\Ui\Component\Listing\Post\Authors
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        if (count($options) >= 1) {
            array_unshift($options, ['label' => __('Select...')->render(), 'value' => 0]);
        }
        return $options;
    }
}
