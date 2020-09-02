<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\View;

/**
 * Class Tag
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales\View
 */
class Tag extends \Magestore\OrderSuccess\Block\Adminhtml\Order\View\Info
{
    /**
     * @return array
     */
    public function getTagList()
    {
        return $this->tagSourceInterface->toOptionArray();
    }

    /**
     * @return array
     */
    public function getTagArray()
    {
        return $this->tagSourceInterface->getOptionArray();
    }

    /**
     * Get current order tags
     *
     * @return string
     */
    public function getCurrentTags()
    {
        $order = $this->getOrder();
        $tags = [];
        if($order->getTagColor()) {
            $tags = explode(',', $order->getTagColor());
        }
        return $tags;
    }

    /**
     * Get tag only
     *
     * @return string
     */
    public function getTagOnly()
    {
        $tags = $this->getCurrentTags();
        if(count($tags) == 1){
            return $tags[0];
        }
        return '';
    }

    /**
     * Get tag label
     *
     * @return string
     */
    public function getTagLabel($tag)
    {
        $tagList = $this->getTagArray();
        $label = isset($tagList[$tag]) ? $tagList[$tag] : '#FFFFFF';
        return $label;
    }

}

