<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Api\Data;

interface CategoryInterface
{
    const ROUTE_CATEGORY = 'category';

    const CATEGORY_ID = 'category_id';

    const NAME = 'name';

    const URL_KEY = 'url_key';

    const STATUS = 'status';

    const SORT_ORDER = 'sort_order';

    const META_TITLE = 'meta_title';

    const META_TAGS = 'meta_tags';

    const META_DESCRIPTION = 'meta_description';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const PARENT_ID = 'parent_id';

    const PATH = 'path';

    const LEVEL = 'level';

    const ROOT_CATEGORY_ID = "0";

    const STORE_ID = "store_id";

    const FIELDS_BY_STORE = [
        'general' => [
            self::NAME,
            self::STATUS,
        ],
        'meta_data' => [
            self::META_TITLE,
            self::META_TAGS,
            self::META_DESCRIPTION
        ]
    ];

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $categoryId
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setCategoryId($categoryId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $urlKey
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * @param string|null $metaTitle
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string|null
     */
    public function getMetaTags();

    /**
     * @param string|null $metaTags
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setMetaTags($metaTags);

    /**
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * @param string|null $metaDescription
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $parentId
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setParentId($parentId);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setPath($path);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $level
     *
     * @return \Amasty\Blog\Api\Data\CategoryInterface
     */
    public function setLevel($level);

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Categories\Collection
     */
    public function getChildrenCollection();

    /**
     * @return bool
     */
    public function hasActiveChildren();
}
