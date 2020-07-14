<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface CategoryInterface
{
    /**
     * Category id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Retrieve Name data wrapper
     *
     * @return string
     */
    public function getName();


    /**
     * Retrieve children ids
     *
     * @return string[]
     */
    public function getChildrenIds();


    /**
     * Retrieve children ids
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\CategoryInterface[]
     */
    public function getChildren();

    /**
     * Retrieve children ids
     *
     * @param $children \Magestore\Webpos\Api\Data\Catalog\CategoryInterface[]
     * @return CategoryInterface
     */
    public function setChildren($children);

    /**
     * Get category image
     *
     * @return string/null
     */
    public function getImage();

    /**
     * Get category image
     *
     * @return string/null
     */
    public function getPosition();

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel();

    /**
     * is first category
     * @return int
     */
    public function isFirstCategory();

    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId();

    /**
     * @codeCoverageIgnoreStart
     * @return string|null
     */
    public function getPath();
}
