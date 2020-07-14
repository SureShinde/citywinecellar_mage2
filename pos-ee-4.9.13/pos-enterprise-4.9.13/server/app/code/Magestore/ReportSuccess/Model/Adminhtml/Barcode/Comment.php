<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Adminhtml\Barcode;
/**
 * Class Comment
 * @package Magestore\ReportSuccess\Model\Adminhtml\Barcode
 */
class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * Comment constructor.
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url
    ){
        $this->url = $url;
    }

    /**
     * @param string $elementValue
     * @return \Magento\Framework\Phrase|string
     */
    public function getCommentText($elementValue)
    {
        $link = $this->url->getUrl('catalog/product_attribute/new');
        return __('Only attributes with unique value can be used. If you don\'t have any besides SKU, create one <a target=\'_blank\' href=%1>here</a>.', $link);
    }
}