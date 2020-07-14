<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content;

use Amasty\Blog\Api\AuthorRepositoryInterface;

class Lists extends AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface
{
    const PAGER_BLOCK_NAME = 'amblog_list_pager';

    /**
     * @var $collection
     */
    protected $collection;

    /**
     * @var bool
     */
    protected $isCategory = false;

    /**
     * @var bool
     */
    protected $isTag = false;

    /**
     * @var null
     */
    private $toolbar = null;

    /**
     * @var \Amasty\Blog\Model\ListsFactory
     */
    private $listsModel;

    /**
     * @var \Amasty\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Amasty\Blog\Api\TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var \Amasty\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Url $urlHelper,
        \Amasty\Blog\Api\TagRepositoryInterface $tagRepository,
        \Amasty\Blog\Api\AuthorRepositoryInterface $authorRepository,
        \Amasty\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\Blog\Api\PostRepositoryInterface $postRepository,
        \Amasty\Blog\Model\ListsFactory $listsModel,
        \Amasty\Blog\Helper\Date $helperDate,
        \Amasty\Blog\Model\UrlResolver $urlResolver,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $settingsHelper, $urlHelper, $urlResolver, $helperDate, $data);
        $this->listsModel = $listsModel;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        $this->authorRepository = $authorRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $categoryTitle = $this->getCategory()
            ? $this->getCategory()->getName()
            : $this->getSettingHelper()->getSeoTitle();
        $this->setTitle($categoryTitle);

        parent::_prepareLayout();

        $this->getToolbar()
            ->setPagerObject($this->listsModel->create())
            ->setLimit($this->getSettingHelper()->getPostsLimit())
            ->setCollection($this->getCollection());

        return $this;
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
            $breadcrumbs->addCrumb(
                'blog',
                [
                    'label' => $this->getSettingHelper()->getBreadcrumb(),
                    'title' => $this->getSettingHelper()->getBreadcrumb(),
                ]
            );
        }
    }

    /**
     * @param $post
     * @return string
     */
    public function getReadMoreUrl($post)
    {
        return $this->getUrlResolverModel()->getPostUrlById($post->getId());
    }

    /**
     * @param \Amasty\Blog\Model\ResourceModel\Posts\Collection $collection
     * @return $this
     */
    private function checkTag($collection)
    {
        if (($id = $this->getRequest()->getParam('id')) && $this->isTag) {
            $collection->addTagFilter($id);
        }

        return $this;
    }

    /**
     * @param \Amasty\Blog\Model\ResourceModel\Posts\Collection $collection
     * @return $this
     */
    private function checkCategory($collection)
    {
        if (($id = $this->getRequest()->getParam('id')) && $this->isCategory) {
            $collection->addCategoryFilter($id);
        }

        return $this;
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $posts = $this->postRepository->getActivePosts();

            $posts->setUrlKeyIsNotNull();
            $posts->setDateOrder();

            $this->checkCategory($posts);
            $this->checkTag($posts);

            $this->collection = $posts;
        }

        return $this->collection;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToolbar()
    {
        if (!$this->toolbar) {
            $toolbar = $this->getLayout()->createBlock(\Amasty\Blog\Block\Content\Lists\Pager::class);
            $this->getLayout()->setBlock(self::PAGER_BLOCK_NAME, $toolbar);
            $this->toolbar = $toolbar;
        }

        return $this->toolbar;
    }

    /**
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToolbarHtml($isAmp = false)
    {
        $template = $isAmp ? 'Amasty_Blog::amp/list/pager.phtml' : 'Amasty_Blog::list/pager.phtml';

        return $this->getToolbar()->setTemplate($template)->toHtml();
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getSettingHelper()->getBlogMetaTitle() ? $this->getSettingHelper()->getBlogMetaTitle()
            : $this->getTitle();
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->getSettingHelper()->getBlogMetaKeywords();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getSettingHelper()->getBlogMetaDescription();
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->getSettingHelper()->getIconColorClass();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Blog\Model\Lists::CACHE_TAG];
    }

    /**
     * @return \Amasty\Blog\Api\TagRepositoryInterface
     */
    public function getTagRepository()
    {
        return $this->tagRepository;
    }

    /**
     * @return AuthorRepositoryInterface
     */
    public function getAuthorRepository()
    {
        return $this->authorRepository;
    }

    /**
     * @return \Amasty\Blog\Api\CategoryRepositoryInterface
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * @return \Amasty\Blog\Api\PostRepositoryInterface|\Amasty\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getPostRepository()
    {
        return $this->postRepository;
    }
}
