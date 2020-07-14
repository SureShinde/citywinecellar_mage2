<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content;

use Amasty\Blog\Block\Content\Post\Details;

class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Blog\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Amasty\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \Amasty\Blog\Helper\Settings
     */
    private $settingsHelper;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var array
     */
    private $data;

    /**
     * @var \Amasty\Blog\Helper\Date
     */
    private $helperDate;

    /**
     * @var \Amasty\Blog\Model\UrlResolver
     */
    private $urlResolver;

    /**
     * @var array
     */
    private $crumbs = [];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Url $urlHelper,
        \Amasty\Blog\Model\UrlResolver $urlResolver,
        \Amasty\Blog\Helper\Date $helperDate,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->urlHelper = $urlHelper;
        $this->settingsHelper = $settingsHelper;
        $this->context = $context;
        $this->data = $data;
        $this->helperDate = $helperDate;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareBreadcrumbs()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $this->addCrumb(
                $breadcrumbs,
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Home'),
                    'link'  => $this->getBaseUrl()
                ]
            );
        }

        return $this;
    }

    /**
     * @param \Magento\Theme\Block\Html\Breadcrumbs $block
     * @param $key
     * @param $data
     */
    protected function addCrumb(\Magento\Theme\Block\Html\Breadcrumbs $block, $key, $data)
    {
        $this->crumbs[$key] = $data;
        $block->addCrumb($key, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function preparePage()
    {
        $this->pageConfig->getTitle()->set($this->getMetaTitle() ?: __('Post'));
        /** @var \Magento\Theme\Block\Html\Title $headingBlock */
        if ($headingBlock = $this->getLayout()->getBlock('page.main.title')) {
            $headingBlock->setPageTitle($this->getTitle());
            $this->pageConfig->setKeywords($this->getKeywords());
            $this->pageConfig->setDescription($this->escapeHtml(str_replace("\n", "", $this->getDescription())));
        }

        $this->prepareBreadcrumbs();
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->preparePage();

        return $this;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?: __('Default Blog Title');
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @param null $post
     * @return bool|string
     */
    public function getAmpHeaderHtml($post = null)
    {
        return $this->getHtml(
            Details::class,
            'Amasty_Blog::amp/post/header.phtml',
            $post
        );
    }

    /**
     * @param null $post
     * @return bool|string
     */
    public function getAuthorHtml($post = null)
    {
        return $this->getHtml(
            Details::class,
            'Amasty_Blog::post/author.phtml',
            $post
        );
    }

    /**
     * @param null $post
     * @return bool|string
     */
    public function getShortCommentsHtml($post = null)
    {
        return $this->getHtml(
            Details::class,
            'Amasty_Blog::post/short_comments.phtml',
            $post
        );
    }

    /**
     * @param $post
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoriesHtml($post = null, $isAmp = false)
    {
        $html = '';
        if ($this->settingsHelper->getUseCategories()) {
            $template = $isAmp ? 'Amasty_Blog::amp/list/categories.phtml' : 'Amasty_Blog::list/categories.phtml';

            $html = $this->getHtml(Details::class, $template, $post);
        }

        return $html;
    }

    /**
     * @param $post
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTagsHtml($post = null, $isAmp = false)
    {
        $html = '';
        if ($this->settingsHelper->getUseTags()) {
            $template = $isAmp ? 'Amasty_Blog::amp/list/tags.phtml' : 'Amasty_Blog::list/tags.phtml';
            $html = $this->getHtml(Details::class, $template, $post);
        }

        return $html;
    }

    /**
     * @param $post
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAuthorPostsHtml($post = null, $isAmp = false)
    {
        $html = '';
        if ($this->settingsHelper->getShowAuthor()
            && $post
            && $post->getAuthor()->getId()
        ) {
            $template = $isAmp ? 'Amasty_Blog::amp/list/author_posts.phtml' : 'Amasty_Blog::list/author_posts.phtml';
            $html = $this->getHtml(Details::class, $template, $post);
        }

        return $html;
    }

    /**
     * @param $blockClass
     * @param $template
     * @param $post
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getHtml($blockClass, $template, $post)
    {
        $block = $this->getLayout()->createBlock($blockClass);
        $html = '';
        if ($block) {
            $block->setPost($post)->setTemplate($template);
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return \Magento\Framework\View\Element\Template\Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return \Amasty\Blog\Helper\Url
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * @return \Amasty\Blog\Model\UrlResolver
     */
    public function getUrlResolverModel()
    {
        return $this->urlResolver;
    }

    /**
     * @return \Amasty\Blog\Helper\Settings
     */
    public function getSettingHelper()
    {
        return $this->settingsHelper;
    }

    /**
     * @param $datetime
     * @return \Magento\Framework\Phrase|string
     */
    public function renderDate($datetime)
    {
        return $this->helperDate->renderDate($datetime);
    }

    /**
     * @return array
     */
    public function getCrumbs()
    {
        return $this->crumbs;
    }
}
