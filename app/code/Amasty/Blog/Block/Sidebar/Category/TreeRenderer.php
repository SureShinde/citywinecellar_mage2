<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Sidebar\Category;

use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Model\ResourceModel\Categories\Collection;
use Magento\Store\Model\Store;

class TreeRenderer extends \Amasty\Blog\Block\Sidebar\AbstractClass
{
    const LEVEL_LIMIT = 3;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var \Amasty\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var null|array
     */
    private $postsCount = null;

    /**
     * @var null|int
     */
    private $categoriesLimit = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Blog\Helper\Settings $settingsHelper,
        \Amasty\Blog\Helper\Date $dateHelper,
        \Amasty\Blog\Helper\Data $dataHelper,
        \Amasty\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $settingsHelper, $dateHelper, $dataHelper, $data);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Amasty_Blog::sidebar/categories/tree.phtml");
        $this->addAmpTemplate('Amasty_Blog::amp/sidebar/categories/tree.phtml');
    }

    /**
     * @param int $parentId
     * @return string
     */
    public function render($parentId = CategoryInterface::ROOT_CATEGORY_ID)
    {
        $this->setParentId($parentId);
        return $this->toHtml();
    }

    /**
     * Render all children for current category path
     *
     * @param int $parentId
     * @return string
     */
    public function renderChildrenItems($parentId)
    {
        return $this->getLayout()
            ->createBlock(self::class)
            ->setCategoriesLimit($this->getCategoriesLimit())
            ->render($parentId);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->categoryRepository->getChildrenCategories(
                $this->getParentId(),
                $this->getCategoriesLimit()
            );
        }

        return $this->collection;
    }

    /**
     * @param $categoryId
     *
     * @return int
     */
    public function getPostsCount($categoryId)
    {
        if ($this->postsCount === null) {
            $this->postsCount = $this->collection->getPostsCount(
                [
                    $this->_storeManager->getStore()->getId(),
                    Store::DEFAULT_STORE_ID
                ]
            );
        }

        return isset($this->postsCount[$categoryId]) ? $this->postsCount[$categoryId] : 0;
    }

    /**
     * @return int|null
     */
    public function getCategoriesLimit()
    {
        return $this->categoriesLimit;
    }

    /**
     * @param $categoriesLimit
     *
     * @return $this
     */
    public function setCategoriesLimit($categoriesLimit)
    {
        $this->categoriesLimit = $categoriesLimit;

        return $this;
    }
}
