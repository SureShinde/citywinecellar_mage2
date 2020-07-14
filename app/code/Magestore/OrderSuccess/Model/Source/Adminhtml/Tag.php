<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\Source\Adminhtml;

/**
 * Class Tag
 * @package Magestore\OrderSuccess\Model\Source\Adminhtml
 */
/**
 * Class Tag
 * @package Magestore\OrderSuccess\Model\Source\Adminhtml
 */
class Tag implements \Magestore\OrderSuccess\Api\Data\TagSourceInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $helper;

    /**
     * Tag constructor.
     * @param \Magestore\OrderSuccess\Helper\Data $helper
     */
    public function __construct(
        \Magestore\OrderSuccess\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }
    
    /**
     * @return array
     */
    public function getTagList()
    {
        $tagArray = unserialize($this->helper->getOrderConfig('tag'));
        return $tagArray;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $tags = [];
        $tags[] = ['value' => 'na', 'label' => '-- '.__('Add Tag').' --'];
        $tags[] = ['value' => 'remove', 'label' => __('Remove All Tags')];
        $tagList = $this->getTagList();
        if(count($tagList) && is_array($tagList)) {
            ;
            foreach ($this->getTagList() as $tag) {
                $tags[] = ['value' => '#' . $tag['color'], 'label' => $tag['title']];
            }
        }
        return $tags;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $tags = [];
        foreach($this->getTagList() as $tag){
            $tags['#'.$tag['color']] = $tag['title'];
        }
        return $tags;
    }

}