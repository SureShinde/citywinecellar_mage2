<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Block\Sidebar\Category\TreeRenderer;

class Categories extends AbstractModel implements \Magento\Framework\DataObject\IdentityInterface, CategoryInterface
{
    const PERSISTENT_NAME = 'amasty_blog_categories';

    const CACHE_TAG = 'amblog_category';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var \Amasty\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Amasty\Blog\Model\ResourceModel\Categories\CollectionFactory
     */
    private $categoriesCollectionFactory;

    public function _construct()
    {
        parent::_construct();
        $this->categoryRepository = $this->getCategoryRepository();
        $this->categoriesCollectionFactory = $this->getCategoriesCollectionFactory();
        $this->_init(\Amasty\Blog\Model\ResourceModel\Categories::class);
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return  self::ROUTE_CATEGORY;
    }

    /**
     * @return array
     */
    public function getStores()
    {
        if (!$this->hasData('stores')) {
            $stores = $this->categoryRepository->getStores($this->getId());
            $storesArray = [];
            foreach ($stores as $store) {
                $storesArray[] = $store['store_id'];
            }
            $this->setData('stores', $storesArray);
        }

        return $this->_getData('stores');
    }

    /**
     * @return AbstractModel
     */
    public function beforeSave()
    {
        $urlHelper = $this->getUrlHelper();
        $urlKey = $this->getUrlKey();
        if (!$urlHelper->validate($urlKey)) {
            $this->setUrlKey($urlHelper->prepare($urlKey));
        }

        if ($this->getCategoryId()) {
            if ($this->getParentId() != $this->getOrigData(CategoryInterface::PARENT_ID)) {
                $this->move($this->getParentId());
            }
        } else {
            $this->setParentCategory();
        }

        return parent::beforeSave();
    }

    /**
     * @return CategoryInterface
     */
    public function getParentCategory()
    {
        if (!$this->getData('parent_category')) {
            $category = $this->categoryRepository->getById($this->getParentId());
            $this->setData('parent_category', $category);
        }

        return $this->getData('parent_category');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [
            \Amasty\Blog\Model\Lists::CACHE_TAG,
            self::CACHE_TAG . '_' . $this->getId()
        ];

        return $identities;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return (int)$this->_getData(CategoryInterface::CATEGORY_ID);
    }

    /**
     * @param int $categoryId
     *
     * @return $this
     */
    public function setCategoryId($categoryId)
    {
        $this->setData(CategoryInterface::CATEGORY_ID, $categoryId);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_getData(CategoryInterface::NAME);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(CategoryInterface::NAME, $name);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlKey()
    {
        return $this->_getData(CategoryInterface::URL_KEY);
    }

    /**
     * @param string $urlKey
     *
     * @return $this
     */
    public function setUrlKey($urlKey)
    {
        $this->setData(CategoryInterface::URL_KEY, $urlKey);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData(CategoryInterface::STATUS);
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(CategoryInterface::STATUS, $status);

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return (int)$this->_getData(CategoryInterface::SORT_ORDER);
    }

    /**
     * @param int $sortOrder
     *
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->setData(CategoryInterface::SORT_ORDER, $sortOrder);

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->_getData(CategoryInterface::META_TITLE);
    }

    /**
     * @param null|string $metaTitle
     *
     * @return $this
     */
    public function setMetaTitle($metaTitle)
    {
        $this->setData(CategoryInterface::META_TITLE, $metaTitle);

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTags()
    {
        return $this->_getData(CategoryInterface::META_TAGS);
    }

    /**
     * @param null|string $metaTags
     *
     * @return $this
     */
    public function setMetaTags($metaTags)
    {
        $this->setData(CategoryInterface::META_TAGS, $metaTags);

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->_getData(CategoryInterface::META_DESCRIPTION);
    }

    /**
     * @param null|string $metaDescription
     *
     * @return $this
     */
    public function setMetaDescription($metaDescription)
    {
        $this->setData(CategoryInterface::META_DESCRIPTION, $metaDescription);

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getData(CategoryInterface::STORE_ID);
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(CategoryInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(CategoryInterface::CREATED_AT);
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(CategoryInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(CategoryInterface::UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(CategoryInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        if (!$this->getData(CategoryInterface::PARENT_ID)) {
            $this->setParentId(0);
        }
        return $this->getData(CategoryInterface::PARENT_ID);
    }

    /**
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->setData(CategoryInterface::PARENT_ID, $parentId);

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getData(CategoryInterface::PATH);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->setData(CategoryInterface::PATH, $path);

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->getData(CategoryInterface::LEVEL);
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->setData(CategoryInterface::LEVEL, $level);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return (bool)$this->getChildrenCollection()->getSize();
    }

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Categories\Collection
     */
    public function getChildrenCollection()
    {
        if (!$this->getData('children_collection')) {
            $collection = $this->categoriesCollectionFactory->create();
            $childrenPath = $this->getPath() . DIRECTORY_SEPARATOR . $this->getCategoryId();
            $collection->addFieldToFilter(CategoryInterface::PARENT_ID, ['eq' => $this->getCategoryId()]);
            $this->setData('children_collection', $collection);
        }

        return $this->getData('children_collection');
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        /**
         * @var \Amasty\Blog\Model\ResourceModel\Categories\Collection $childrenCollection
         */
        $childrenCollection = $this->getChildrenCollection();
        foreach ($childrenCollection as $childCategory) {
            $childCategory->setParentId($this->getParentId());
            $this->categoryRepository->save($childCategory);
        }

        return parent::beforeDelete();
    }

    /**
     * @return bool
     */
    public function hasActiveChildren()
    {
        $collection = $this->categoryRepository->getActiveCategories();
        $collection->addFieldToFilter(self::PARENT_ID, $this->getCategoryId());

        return (bool)count($collection);
    }

    /**
     * @param int $newParentId
     * @return $this
     */
    public function move($newParentId)
    {
        if ($newParentId) {
            $newParent = $this->categoryRepository->getById($newParentId);
            $startPath = $newParent->getPath();
            $startLevel = $newParent->getLevel();
        } else {
            $startPath = self::ROOT_CATEGORY_ID;
            $startLevel = 0;
        }

        $this->setPath(
            $newParentId ?
                $startPath . DIRECTORY_SEPARATOR . $newParentId
                : $newParentId
        );
        $this->setLevel($startLevel + 1);

        $this->updateChildrenAfterMove($this);

        return $this;
    }

    /**
     * @param CategoryInterface $parentCategory
     * @throws \Exception
     * @return $this
     */
    public function updateChildrenAfterMove($parentCategory)
    {
        foreach ($parentCategory->getChildrenCollection() as $child) {
            $newLevel = $parentCategory->getLevel() + 1;
            if ($newLevel > TreeRenderer::LEVEL_LIMIT) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(
                        'You have exceeded the category tree depth which is limited by %1.',
                        TreeRenderer::LEVEL_LIMIT
                    )
                );
            }
            $child->setLevel($newLevel);
            $child->setPath($parentCategory->getPath() . DIRECTORY_SEPARATOR . $parentCategory->getCategoryId());
            $this->updateChildrenAfterMove($child);
            $this->categoryRepository->save($child);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setParentCategory()
    {
        if (!$this->getCategoryId()) {
            if ($this->getParentId()) {
                $this->setPath(
                    $this->getParentCategory()->getPath() ?
                        $this->getParentCategory()->getPath()
                        . DIRECTORY_SEPARATOR
                        . $this->getParentCategory()->getCategoryId()
                        : $this->getParentCategory()->getCategoryId()
                );
                $this->setLevel($this->getParentCategory()->getLevel() + 1);
            } else {
                $this->setPath(self::ROOT_CATEGORY_ID);
                $this->setLevel(1);
            }
        }

        return $this;
    }
}
