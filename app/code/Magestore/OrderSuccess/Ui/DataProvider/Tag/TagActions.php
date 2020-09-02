<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider\Tag;

use Magento\Framework\UrlInterface;
use Magestore\OrderSuccess\Model\Source\Adminhtml\Tag as TagSource;

/**
 * Class TagActions
 * @package Magestore\OrderSuccess\Ui\DataProvider\Tag
 */
class TagActions
{

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * context
     *
     * @var \Magestore\OrderSuccess\Ui\DataProvider\Context
     */
    protected $context;

    /**
     * context
     *
     * @var \Magestore\OrderSuccess\Model\Source\Adminhtml\Tag;
     */
    protected $tag;

    /**
     * @var string
     */
    protected $urlPath = '*/order/setTag';


    /**
     * TagActions constructor.
     * @param UrlInterface $urlBuilder
     * @param TagSource $tag
     */
    public function __construct(
        UrlInterface $urlBuilder,
        TagSource $tag
    ){
        $this->urlBuilder = $urlBuilder;
        $this->tag = $tag;
    }

    /**
     * get actions
     *
     * @return array
     */
    public function getActions($posistion)
    {
        $actions = [];
        $tagList = $this->tag->getTagList();
        if(count($tagList) && is_array($tagList)) {
            foreach($tagList as $tag) {
                $actions[] = [
                    'type' => '#'.$tag['color'],
                    'label' => $tag['title'],
                    'url' => $this->urlBuilder->getUrl($this->urlPath,
                        [
                            'tag' => $tag['color'],
                            'order_position' => $posistion
                        ]),
                ];
            }
        }
        return $actions;
    }
}