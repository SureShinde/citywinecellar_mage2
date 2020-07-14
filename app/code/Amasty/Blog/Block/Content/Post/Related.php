<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content\Post;

use Amasty\Blog\Api\Data\PostInterface;
use Magento\Framework\View\Element\Template;

class Related extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var PostInterface
     */
    private $post;

    /**
     * @var \Amasty\Blog\Model\ResourceModel\Posts\Collection
     */
    protected $collection = null;

    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Amasty\Blog\Helper\Date
     */
    private $helperDate;

    /**
     * @var \Amasty\Blog\Model\UrlResolver
     */
    private $urlResolver;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        \Amasty\Blog\Helper\Date $helperDate,
        \Amasty\Blog\Model\UrlResolver $urlResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->postRepository = $postRepository;
        $this->helperDate = $helperDate;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getCollection()
    {
        if (!$this->collection && $this->getPost() && $this->getPost()->getRelatedPostIds()) {
            $postIds = explode(',', $this->getPost()->getRelatedPostIds());
            $this->collection = $this->postRepository->getActivePosts()
                ->addFieldToFilter(PostInterface::POST_ID, ['in' => $postIds])
                ->setUrlKeyIsNotNull()
                ->setDateOrder();
        }

        return $this->collection;
    }

    /**
     * @return PostInterface
     */
    public function getPost()
    {
        if ($this->post === null) {
            $this->post = $this->registry->registry('current_post');
        }

        return $this->post;
    }

    /**
     * @param $post
     * @return string
     */
    public function getReadMoreUrl($post)
    {
        return $this->urlResolver->getPostUrlById($post->getId());
    }

    /**
     * @param $datetime
     * @return \Magento\Framework\Phrase|string
     */
    public function renderDate($datetime)
    {
        return $this->helperDate->renderDate($datetime);
    }
}
