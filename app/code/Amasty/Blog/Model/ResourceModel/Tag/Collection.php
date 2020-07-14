<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Tag;

use Amasty\Blog\Api\Data\TagInterface;
use Amasty\Blog\Model\ResourceModel\Tag as TagResource;
use Amasty\Blog\Model\ResourceModel\Traits\CollectionTrait;
use Magento\Store\Model\Store;
use Amasty\Blog\Model\ResourceModel\Traits\StoreCollectionTrait;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    use StoreCollectionTrait;
    use CollectionTrait;

    const MULTI_STORE_FIELDS_MAP = [
        TagInterface::NAME => 'IFNULL(noDefaultStore.name, store.name)',
        TagInterface::META_TITLE => 'IFNULL(noDefaultStore.meta_title, store.meta_title)',
        TagInterface::META_DESCRIPTION => 'IFNULL(noDefaultStore.meta_description, store.meta_description)',
        TagInterface::META_TAGS => 'IFNULL(noDefaultStore.meta_tags, store.meta_tags)',
    ];

    /**
     * @var bool
     */
    private $addWheightData = false;

    /**
     * @var bool
     */
    private $postDataJoined = false;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @var string
     */
    protected $_idFieldName = 'tag_id';

    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'tag_id' => 'main_table.tag_id'
        ]
    ];

    public function _construct()
    {
        $this->_init(\Amasty\Blog\Model\Tag::class, \Amasty\Blog\Model\ResourceModel\Tag::class);
    }

    /**
     * @return $this
     */
    public function addCount()
    {
        $this->getSelect()
            ->joinLeft(
                ['posttag' => $this->getTable('amasty_blog_posts_tag')],
                'main_table.tag_id = posttag.tag_id',
                ['COUNT(posttag.`tag_id`) as count']
            );
        $this->getSelect()->group('main_table.tag_id');

        return $this;
    }

    /**
     * @param null $store
     *
     * @return $this
     */
    public function addWeightData($store = null)
    {
        $this->addWheightData = true;
        $this->joinPostData();
        $this->getSelect()
            ->columns(['post_count' => new \Zend_Db_Expr('COUNT(post.post_id)')])
            ->group('main_table.tag_id');

        if ($store) {
            $store = is_array($store) ? $store : [$store, Store::DEFAULT_STORE_ID];
            $store = "'" . implode("','", $store) . "'";
            $postStoreTable = $this->getTable('amasty_blog_posts_store');
            $this->getSelect()
                ->join(['post_store' => $postStoreTable], 'post.post_id = post_store.post_id', [])
                ->where(new \Zend_Db_Expr('post_store.store_id IN (' . $store . ')'));
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function joinPostData()
    {
        if ($this->postDataJoined) {
            return $this;
        }

        $this->postDataJoined = true;

        $postTagTable = $this->getTable('amasty_blog_posts_tag');
        $this->getSelect()->join(['post' => $postTagTable], "post.tag_id = main_table.tag_id", []);

        return $this;
    }

    /**
     * @param $count
     *
     * @return $this
     */
    public function setMinimalPostCountFilter($count)
    {
        if ($this->addWheightData) {
            $this->getSelect()->having('COUNT(post.post_id) >= ?', $count);
        }

        return $this;
    }

    /**
     * @param $status
     *
     * @return $this
     */
    public function setPostStatusFilter($status)
    {
        $status = is_array($status) ? $status : [$status];

        $postTable = $this->getTable('amasty_blog_posts');
        $this->getSelect()
            ->join(['postEntity' => $postTable], 'post.post_id = postEntity.post_id', [])
            ->where('postEntity.status IN (?)', $status);

        return $this;
    }

    /**
     * @return $this
     */
    public function setNameOrder()
    {
        $this->getSelect()->order('name ASC');

        return $this;
    }

    /**
     * @param $postIds
     *
     * @return $this
     */
    public function addPostFilter($postIds)
    {
        $postIds = is_array($postIds) ? $postIds : [$postIds];

        $this->joinPostData();
        $this->getSelect()->where('post.post_id IN (?)', $postIds);

        return $this;
    }

    /**
     * @param array $tagIds
     * @return $this
     */
    public function addIdFilter($tagIds = [])
    {
        if (!is_array($tagIds)) {
            $tagIds = [$tagIds];
        }
        $this->addFieldToFilter('tag_id', ['in' => $tagIds]);

        return $this;
    }

    /**
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->renderFilters();
        if ($this->queryText) {
            $this->getSelect()->group('main_table.tag_id');
        }
    }

    /**
     * @return string
     */
    public function getQueryText()
    {
        return $this->queryText;
    }

    /**
     * @param $queryText
     * @return $this
     */
    public function setQueryText($queryText)
    {
        $this->queryText = $queryText;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreTable()
    {
        return $this->getTable(TagResource::STORE_TABLE_NAME);
    }
}
