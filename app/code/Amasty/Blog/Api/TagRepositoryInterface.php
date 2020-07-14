<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Api;

use Amasty\Blog\Model\ResourceModel\Tag\Collection;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface TagRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Blog\Api\Data\TagInterface $tag
     *
     * @return \Amasty\Blog\Api\Data\TagInterface
     */
    public function save(\Amasty\Blog\Api\Data\TagInterface $tag);

    /**
     * Get by id
     *
     * @param int $tagId
     *
     * @return \Amasty\Blog\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($tagId);

    /**
     * @param $urlKey
     * @return \Amasty\Blog\Api\Data\TagInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * Delete
     *
     * @param \Amasty\Blog\Api\Data\TagInterface $tag
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Blog\Api\Data\TagInterface $tag);

    /**
     * Delete by id
     *
     * @param int $tagId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($tagId);

    /**
     * Lists
     *
     * @param string[] $tags
     *
     * @return Collection
     */
    public function getList($tags);

    /**
     * @return \Amasty\Blog\Model\Tag
     */
    public function getTagModel();

    /**
     * @return Collection
     */
    public function getTagCollection();

    /**
     * @param int $postId
     * @param int $storeId
     * @return Collection
     */
    public function getTagsByPost($postId, $storeId);

    /**
     * @param array $tagsIds
     * @return Collection
     */
    public function getTagsByIds($tagsIds = []);

    /**
     * @param null $storeId
     * @return Collection
     */
    public function getActiveTags($storeId = null);

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getAllTags();

    /**
     * @param $tagId
     * @param int $storeId
     * @param bool $isAddDefaultStore
     * @return \Magento\Framework\DataObject
     */
    public function getByIdAndStore($tagId, $storeId = 0, $isAddDefaultStore = true);
}
