<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Log;

/**
 * Interface CategoryDeletedInterface
 * @package Magestore\Webpos\Api\Data\Log
 */
interface CategoryDeletedInterface {
    const ID = 'id';
    const CATEGORY_ID = 'category_id';
    const ROOT_CATEGORY_ID = 'root_category_id';
    const DELETED_AT = 'deleted_at';

    /**
     * Get Id
     *
     * @return int
     */
    public function getId();
    /**
     * Set Id
     *
     * @param int $id
     * @return CategoryDeletedInterface
     */
    public function setId($id);

    /**
     * Get category id
     *
     * @return int
     */
    public function getCategoryId();
    /**
     * Set category id
     *
     * @param int $categoryId
     * @return CategoryDeletedInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Get category id
     *
     * @return int
     */
    public function getRootCategoryId();
    /**
     * Set category id
     *
     * @param int $rootCategoryId
     * @return CategoryDeletedInterface
     */
    public function setRootCategoryId($rootCategoryId);
    /**
     * Get deleted at
     *
     * @return string
     */
    public function getDeletedAt();
    /**
     * Set deleted at
     *
     * @param string $deletedAt
     * @return CategoryDeletedInterface
     */
    public function setDeletedAt($deletedAt);
}
