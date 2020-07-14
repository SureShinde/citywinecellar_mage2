<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Adminhtml\Posts\Edit;

class PreviewJsInit extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getPreviewActionUrl()
    {
        return $this->getUrl('amasty_blog/posts/preview');
    }

    /**
     * @return string
     */
    public function getPreviewFrontUrl()
    {
        return $this->url->getUrl('amblog/post/preview');
    }
}
