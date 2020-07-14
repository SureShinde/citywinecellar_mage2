<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Amasty\Blog\Model\ResourceModel\Posts as PostsResource;

/**
 * @api
 */
interface PostRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Blog\Api\Data\PostInterface $post
     *
     * @return \Amasty\Blog\Api\Data\PostInterface
     */
    public function save(\Amasty\Blog\Api\Data\PostInterface $post);

    /**
     * Get by id
     *
     * @param int $postId
     *
     * @return \Amasty\Blog\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($postId);

    /**
     * @param $urlKey
     * @return \Amasty\Blog\Api\Data\PostInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * @param $urlKey
     * @return \Amasty\Blog\Api\Data\PostInterface
     */
    public function getByUrlKeyWithAllStatuses($urlKey);

    /**
     * @return \Amasty\Blog\Api\Data\PostInterface
     */
    public function getPost();

    /**
     * Delete
     *
     * @param \Amasty\Blog\Api\Data\PostInterface $post
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Blog\Api\Data\PostInterface $post);

    /**
     * Delete by id
     *
     * @param int $postId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($postId);

    /**
     * @param int $tagId
     *
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getTaggedPosts($tagId);

    /**
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection;
     */
    public function getPostCollection();

    /**
     * @param $page
     * @return PostsResource\Collection
     */
    public function getPostsByPage($page);

    /**
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getRecentPosts();

    /**
     * @param null $storeId
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getActivePosts($storeId = null);

    /**
     * @param null $storeId
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getFeaturedPosts($storeId = null);
}
