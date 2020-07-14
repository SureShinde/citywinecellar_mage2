<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Comments;

use Amasty\Blog\Model\ResourceModel\Comments;
use Amasty\Blog\Model\Source\CommentStatus;

/**
 * Class
 */
class Collection extends \Amasty\Blog\Model\ResourceModel\Abstracts\Collection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'comment_id';

    /**
     * @var string
     */
    protected $storeIds = '';

    /**
     * @var array
     */
    protected $_map = [
        'fields' => [
            'comment_id' => 'main_table.comment_id'
        ]
    ];

    public function _construct()
    {
        $this->_init(\Amasty\Blog\Model\Comments::class, Comments::class);
    }

    /**
     * @return $this
     */
    public function addPosts()
    {
        $this->getSelect()
            ->joinLeft(
                ['posts' => $this->getTable('amasty_blog_posts')],
                'main_table.post_id = posts.post_id',
                ['posts.title as title']
            );

        return $this;
    }

    /**
     * @param null $ownerSessionId
     * @return $this
     */
    public function addActiveFilter($ownerSessionId = null)
    {
        if ($ownerSessionId) {
            $activeStatus = CommentStatus::STATUS_APPROVED;
            $pendingStatus = CommentStatus::STATUS_PENDING;
            $this->getSelect()
                ->where(
                    new \Zend_Db_Expr(
                        '(main_table.status = "' . $activeStatus . '") OR 
                        ((main_table.status = "' . $pendingStatus . '") AND 
                        (main_table.session_id = "' . $ownerSessionId . '"))'
                    )
                );
        } else {
            $this->addFieldToFilter('status', CommentStatus::STATUS_APPROVED);
        }

        return $this;
    }

    /**
     * @param $postId
     * @return $this
     */
    public function addPostFilter($postId)
    {
        $this->addFieldToFilter('post_id', $postId);

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->getSelect()->order('main_table.created_at ' . $dir);

        return $this;
    }

    /**
     * @return $this
     */
    public function setNotReplies()
    {
        $this->getSelect()->where('main_table.reply_to IS NULL');

        return $this;
    }

    /**
     * @param $commentId
     * @return $this
     */
    public function setReplyToFilter($commentId)
    {
        $this->getSelect()
            ->where('main_table.reply_to = ?', $commentId);

        return $this;
    }

    /**
     * @param $storeIds
     * @return $this|\Amasty\Blog\Model\ResourceModel\Abstracts\Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->storeIds = $storeIds;

        return $this;
    }

    /**
     * @param null|array $storeIds
     *
     * @return $this
     */
    protected function applyStoreFilter($storeIds = null)
    {
        $storeIds = $storeIds ?: $this->storeIds;
        if ($storeIds) {
            if (!is_array($storeIds)) {
                $storeIds = [$storeIds];
            }
            $storeIds[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            $storeIds = "'" . implode("','", $storeIds) . "'";
            $this->getSelect()->where(new \Zend_Db_Expr('main_table.store_id IN (' . $storeIds . ')'));
        }

        return $this;
    }
}
