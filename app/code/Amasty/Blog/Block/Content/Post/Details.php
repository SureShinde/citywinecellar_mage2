<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content\Post;

use Magento\Framework\DataObjectFactory as ObjectFactory;

class Details extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Blog\Helper\Date
     */
    private $helperDate;

    /**
     * @var \Amasty\Blog\Helper\Settings
     */
    public $helperSettings;

    /**
     * @var \Amasty\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var \Amasty\Blog\Api\TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var \Amasty\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;

    /**
     * @var \Amasty\Blog\Model\Posts
     */
    private $post;

    /**
     * @var ObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Date $helperDate,
        \Amasty\Blog\Api\TagRepositoryInterface $tagRepository,
        \Amasty\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\Blog\Api\CommentRepositoryInterface $commentRepository,
        \Amasty\Blog\Helper\Settings $helperSettings,
        ObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperDate = $helperDate;
        $this->helperSettings = $helperSettings;
        $this->commentRepository = $commentRepository;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->context = $context;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param $post
     *
     * @return $this
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return \Amasty\Blog\Model\Posts
     */
    public function getPost()
    {
        return $this->post;
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
     * @return string
     */
    public function getCommentsUrl()
    {
        return $this->getPost()->getUrl() . "#comments";
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCommentsCount()
    {
        $commentsCollection = $this->commentRepository->getCommentsInPost($this->getPost()->getId())->addActiveFilter();

        return $commentsCollection->getSize();
    }

    /**
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTagsHtml($isAmp = false)
    {
        $template = $isAmp ? 'Amasty_Blog::amp/list/tags.phtml' : 'Amasty_Blog::list/tags.phtml';

        return $this->getHtml(\Amasty\Blog\Block\Content\Post\Details::class, $template);
    }

    /**
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoriesHtml($isAmp = false)
    {
        $template = $isAmp ? 'Amasty_Blog::amp/list/categories.phtml' : 'Amasty_Blog::list/categories.phtml';

        return $this->getHtml(\Amasty\Blog\Block\Content\Post\Details::class, $template);
    }

    /**
     * @param $blockClass
     * @param $template
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getHtml($blockClass, $template)
    {
        $block = $this->getLayout()->createBlock($blockClass);
        $html = '';
        if ($block) {
            $block->setPost($this->getPost())->setTemplate($template);
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return array|\Magento\Framework\DataObject[]
     */
    public function getTags()
    {
        if ($this->getPost()->isPreviewPost()) {
            $result = [];
            $tags = $this->getPost()->getData('tags');
            $tagsArray = explode(',', $tags);
            foreach ($tagsArray as $tag) {
                if ($tag) {
                    $result[] = $this->dataObjectFactory->create(
                        [
                            'data' => [
                                'name' => $tag
                            ]
                        ]
                    );
                }
            }

            return $result;
        }

        return $this->tagRepository->getTagsByIds($this->getPost()->getTagIds())->getItems();
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategories()
    {
        $categories = $this->getPost()->getCategories();
        if (!is_array($categories)) {
            $categories = explode(',', $categories);
        }

        return $this->categoryRepository->getCategoriesByIds($categories);
    }

    /**
     * @return bool
     */
    public function showAuthor()
    {
        return $this->helperSettings->getShowAuthor();
    }

    /**
     * @return bool
     */
    public function useSocialProfile()
    {
        return (bool)$this->getPost()->getPostedBy() && (bool)$this->getSocialProfileUrl();
    }

    /**
     * @return string|null
     */
    public function getSocialProfileUrl()
    {
        $url = $this->getPost()->getFacebookProfile();
        if (!$url) {
            $url = $this->getPost()->getTwitterProfile();
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->helperSettings->getIconColorClass();
    }
}
