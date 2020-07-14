<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Categories;

use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Model\ResourceModel\Traits\StoreCollectionTrait;
use Amasty\Blog\Model\ResourceModel\Categories;

class Collection extends \Amasty\Blog\Model\ResourceModel\Abstracts\Collection
{
    use StoreCollectionTrait;

    const MULTI_STORE_FIELDS_MAP = [
        CategoryInterface::STATUS => 'IFNULL(noDefaultStore.status, store.status)',
        CategoryInterface::NAME => 'IFNULL(noDefaultStore.name, store.name)',
        CategoryInterface::META_TITLE => 'IFNULL(noDefaultStore.meta_title, store.meta_title)',
        CategoryInterface::META_DESCRIPTION => 'IFNULL(noDefaultStore.meta_description, store.meta_description)',
        CategoryInterface::META_TAGS => 'IFNULL(noDefaultStore.meta_tags, store.meta_tags)',
    ];

    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'category_id' => 'main_table.category_id'
        ]
    ];

    public function _construct()
    {
        $this->_init(
            \Amasty\Blog\Model\Categories::class,
            \Amasty\Blog\Model\ResourceModel\Categories::class
        );
    }

    /**
     * @param $direction
     * @return $this
     */
    public function setSortOrder($direction)
    {
        $this->getSelect()->order("main_table.sort_order {$direction}");

        return $this;
    }

    /**
     * @param $postId
     *
     * @return $this
     */
    public function addPostFilter($postId)
    {
        $postTable = $this->getTable('amasty_blog_posts_category');

        $this->getSelect()
            ->join(['post' => $postTable], "post.category_id = main_table.category_id", [])
            ->where("post.post_id = ?", $postId);

        return $this;
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function addIdFilter($categoryIds = [])
    {
        if (!is_array($categoryIds)) {
            $categoryIds = [$categoryIds];
        }
        $this->addFieldToFilter('category_id', ['in' => $categoryIds]);

        return $this;
    }

    /**
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        parent::renderFilters();
        if ($this->getQueryText()) {
            $this->getSelect()->group('main_table.category_id');
        }
    }

    /**
     * @param array $stores
     *
     * @return array
     */
    public function getPostsCount($stores)
    {
        $select = $this->getConnection()->select()
            ->from(
                ['posts_cat' => $this->getTable('amasty_blog_posts_category')],
                ['category' => 'posts_cat.category_id', 'posts_count' => 'COUNT(posts_cat.category_id)']
            )->join(
                ['posts' => $this->getTable('amasty_blog_posts')],
                'posts.post_id = posts_cat.post_id AND posts.status = 2',
                []
            )->join(
                ['posts_store' => $this->getTable('amasty_blog_posts_store')],
                'posts_store.post_id = posts.post_id',
                []
            )->where(
                'posts_store.store_id IN (?)',
                $stores
            )
            ->group('category');

        return $this->getConnection()->fetchPairs($select);
    }

    /**
     * @param $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()->where(self::MULTI_STORE_FIELDS_MAP[CategoryInterface::STATUS] . '=' . $status);

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreTable()
    {
        return $this->getTable(Categories::STORE_TABLE_NAME);
    }
}
