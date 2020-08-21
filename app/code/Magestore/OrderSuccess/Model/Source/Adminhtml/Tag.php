<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\Source\Adminhtml;

/**
 * Source Tag
 */
class Tag implements \Magestore\OrderSuccess\Api\Data\TagSourceInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $helper;

    /**
     * Tag constructor.
     *
     * @param \Magestore\OrderSuccess\Helper\Data $helper
     */
    public function __construct(
        \Magestore\OrderSuccess\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get Tag List
     *
     * @return array
     */
    public function getTagList()
    {
        /** @var \Magento\Framework\Serialize\Serializer\Serialize $serialize */
        $serialize = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Framework\Serialize\Serializer\Serialize::class);
        $tagArray = $serialize->unserialize($this->helper->getOrderConfig('tag'));
        return $tagArray;
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $tags = [];
        $tags[] = ['value' => 'na', 'label' => '-- ' . __('Add Tag') . ' --'];
        $tags[] = ['value' => 'remove', 'label' => __('Remove All Tags')];
        $tagList = $this->getTagList();
        if (count($tagList) && is_array($tagList)) {
            foreach ($this->getTagList() as $tag) {
                $tags[] = ['value' => '#' . $tag['color'], 'label' => $tag['title']];
            }
        }
        return $tags;
    }

    /**
     * Get Option Array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $tags = [];
        foreach ($this->getTagList() as $tag) {
            $tags['#' . $tag['color']] = $tag['title'];
        }
        return $tags;
    }
}
