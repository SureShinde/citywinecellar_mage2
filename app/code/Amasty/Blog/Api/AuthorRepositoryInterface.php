<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Api;

use Amasty\Blog\Model\ResourceModel\Author\Collection;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface AuthorRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Blog\Api\Data\AuthorInterface $author
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function save(\Amasty\Blog\Api\Data\AuthorInterface $author);

    /**
     * Get by id
     *
     * @param int $authorId
     *
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($authorId);

    /**
     * @param $urlKey
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * @param $name
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function getByName($name);

    /**
     * Delete
     *
     * @param \Amasty\Blog\Api\Data\AuthorInterface $author
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Blog\Api\Data\AuthorInterface $author);

    /**
     * Delete by id
     *
     * @param int $authorId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($authorId);

    /**
     * Lists
     *
     * @param string[] $authors
     *
     * @return Collection
     */
    public function getList($authors);

    /**
     * @return \Amasty\Blog\Model\Author
     */
    public function getAuthorModel();

    /**
     * @return Collection
     */
    public function getAuthorCollection();

    /**
     * @param string $name
     * @param string $facebookProfile
     * @param string $twitterProfile
     * @return \Amasty\Blog\Api\Data\AuthorInterface
     */
    public function createAuthor($name, $facebookProfile, $twitterProfile);

    /**
     * @param int $authorId
     * @param int $storeId
     * @param bool $isAddDefaultStore
     * @return \Magento\Framework\DataObject
     */
    public function getByIdAndStore($authorId, $storeId = 0, $isAddDefaultStore = true);
}
