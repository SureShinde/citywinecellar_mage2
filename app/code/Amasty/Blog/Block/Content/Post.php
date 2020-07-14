<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content;

use Amasty\Blog\Model\NetworksFactory;
use Magento\Cms\Model\Template\Filter;

class Post extends AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Amasty\Blog\Model\PostsFactory
     */
    private $postRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var NetworksFactory
     */
    private $networksModel;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Url $urlHelper,
        Filter $filter,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        NetworksFactory $networksModel,
        \Amasty\Blog\Model\UrlResolver $urlResolver,
        \Amasty\Blog\Helper\Date $helperDate,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $settingsHelper, $urlHelper, $urlResolver, $helperDate, $data);
        $this->postRepository = $postRepository;
        $this->registry = $registry;
        $this->filter = $filter;
        $this->networksModel = $networksModel;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return \Amasty\Blog\Model\Posts|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPost()
    {
        $post = $this->registry->registry('current_post');
        if (!$post) {
            if ($postId = $this->getRequest()->getParam('id')) {
                $post = $this->postRepository->getById($postId);
                $this->registry->register('current_post', $post, true);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Unknown post id.'));
            }
        }

        return $post;
    }

    /**
     * @return array
     */
    public function getJsonMicroData()
    {
        $resultArray = [$this->jsonEncoder->encode($this->generateMainMicroData())];

        $breadCrumbItems = $this->getBreadCrumbData();
        if ($breadCrumbItems) {
            $resultArray[] = $this->jsonEncoder->encode(
                [
                    '@context'        => 'http://schema.org',
                    '@type'           => 'BreadcrumbList',
                    'itemListElement' => $breadCrumbItems
                ]
            );
        }

        return $resultArray;
    }

    /**
     * @return array
     */
    private function generateMainMicroData()
    {
        $main = [
            '@context'      => 'http://schema.org',
            '@type'         => 'BlogPosting',
            'author'        => [
                "@type" => 'Person',
                "name"  => $this->escapeHtml($this->getPost()->getPostedBy() ?: 'undefined')
            ],
            'datePublished' => $this->escapeHtml($this->getPost()->getPublishedAt()),
            'dateModified'  => $this->escapeHtml($this->getPost()->getUpdatedAt()),
            'name'          => $this->escapeHtml($this->getPost()->getTitle()),
            'description'   => $this->escapeHtml($this->getPost()->getShortContent()),
            'image'         => $this->getPost()->getPostImageSrc(),
            'mainEntityOfPage'
                            => $this->escapeUrl($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true])),
            'headline'      => $this->escapeHtml($this->getPost()->getTitle())
        ];

        $orgName = $this->getSettingHelper()->getModuleConfig('search_engine/organization_name');
        if ($orgName) {
            $main['publisher'] = [
                "@type" => 'Organization',
                'url'   => $this->_urlBuilder->getBaseUrl(),
                "name"  => $this->escapeHtml($orgName)
            ];

            $logoBlock = $this->getLayout()->getBlock('logo');
            if ($logoBlock) {
                $main['publisher']['logo'] = $logoBlock = $logoBlock->getLogoSrc();
            }
        }

        return $main;
    }

    /**
     * @return array
     */
    private function getBreadCrumbData()
    {
        $items = [];
        $position = 0;
        $breadcrumbs = $this->getCrumbs();
        foreach ($breadcrumbs as $breadcrumb) {
            if (!isset($breadcrumb['link']) || !$breadcrumb['link']) {
                continue;
            }

            $items []= [
                '@type' => 'ListItem',
                'position' => ++$position,
                'item' => [
                    '@id' => $breadcrumb['link'],
                    'name' => $breadcrumb['label']
                ]
            ];
        }

        return $items;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        if ($post = $this->getPost()) {
            $this->setTitle($post->getTitle());
        }

        parent::_prepareLayout();

        return $this;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMetaTitle()
    {
        $metaTitle = '';
        $helper = $this->getSettingHelper();
        $post = $this->getPost();
        if ($post) {
            $metaTitle = $post->getMetaTitle()
                ? $helper->getSuffixMetaTitle($helper->getPrefixTitle($post->getMetaTitle()))
                : $helper->getPrefixTitle($post->getTitle());
        }

        return $metaTitle;
    }

    /**
     * @return mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDescription()
    {
        $post = $this->getPost();

        return $post ? $post->getMetaDescription() : '';
    }

    /**
     * @return mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getKeywords()
    {
        $post = $this->getPost();

        return $post ? $post->getMetaTags() : '';
    }

    /**
     * @return AbstractBlock|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareBreadcrumbs()
    {
        parent::prepareBreadcrumbs();

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $this->addCrumb(
                $breadcrumbs,
                'blog',
                [
                    'label' => $this->getSettingHelper()->getBreadcrumb(),
                    'title' => $this->getSettingHelper()->getBreadcrumb(),
                    'link'  => $this->getUrlResolverModel()->getBlogUrl(),
                ]
            );

            $this->addCrumb(
                $breadcrumbs,
                'post',
                [
                    'label' => $this->getTitle(),
                    'title' => $this->getTitle(),
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getSocialHtml()
    {
        return $this->getChildHtml('amblog_social');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHelpfulHtml()
    {
        $html = '';
        $block = $this->getChildBlock('amblog_helpful');
        if ($block) {
            $block->setPost($this->getPost());
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->getSettingHelper()->getIconColorClass();
    }

    /**
     * @return bool
     */
    public function getShowPrintLink()
    {
        return $this->getSettingHelper()->getShowPrintLink();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasThumbnailUrl()
    {
        $post = $this->getPost();
        if ($post) {
            return (bool)$post->getThumbnailUrl();
        }

        return false;
    }

    /**
     * @return mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getThumbnailUrl()
    {
        $url = '';
        $post = $this->getPost();
        if ($post) {
            $url = $post->getThumbnailUrl();
            $url = $this->filter->filter($url);
        }

        return $url;
    }

    /**
     * @return array|string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdentities()
    {
        return [\Amasty\Blog\Model\Posts::CACHE_TAG . '_' . $this->getPost()->getId()];
    }

    /**
     * @return mixed
     */
    public function getNetworksModel()
    {
        return $this->networksModel->create();
    }

    /**
     * @return bool
     */
    public function getUseCommentsGlobal()
    {
        return $this->getSettingHelper()->getUseComments();
    }
}
