<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Amp;

use Magento\Framework\View\Element\Template;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Theme\Model\Favicon\Favicon
     */
    private $favicon;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Amasty\Blog\Block\Html\Header\Logo
     */
    private $logo;

    /**
     * @var \Magento\Theme\Block\Html\Title
     */
    private $title;

    public function __construct(
        Template\Context $context,
        \Magento\Theme\Model\Favicon\Favicon $favicon,
        \Amasty\Blog\Block\Html\Header\Logo $logo,
        \Magento\Theme\Block\Html\Title $title,
        array $data = []
    ) {
        $this->favicon = $favicon;
        $this->assetRepo = $context->getAssetRepository();
        $this->logo = $logo;
        $this->title = $title;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCanonicalUrl()
    {
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->pageConfig->getDescription();
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        $actionName = $this->getRequest()->getActionName();

        $result = [];
        switch ($actionName) {
            case 'post':
                $result = $this->getPostMetaData();
                break;
            case 'category':
                $result = $this->getCategoryMetaData();
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getPostMetaData()
    {
        $postBlock = $this->getLayout()->getBlock('post');

        $result = null;
        if ($postBlock) {
            $post = $postBlock->getPost();
            $result = [
                'published_at' => $post->getPublishedAt(),
                'updated_at' => $post->getUpdatedAt(),
                'posted_by' => $post->getPostedBy()
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getCategoryMetaData()
    {
        $category = $this->getLayout()->getBlock('amblog.content.list')->getCategory();

        $result = null;
        if ($category) {
            $result = [
                'published_at' => $category->getCreatedAt(),
                'updated_at' => $category->getUpdatedAt()
            ];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getFaviconUrl()
    {
        $faviconFile = $this->favicon->getFaviconFile() ? : $this->favicon->getDefaultFavicon();
        $asset = $this->assetRepo->createAsset($faviconFile);

        return $asset->getUrl();
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logo->getLogoUrl();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title->getPageHeading();
    }
}
