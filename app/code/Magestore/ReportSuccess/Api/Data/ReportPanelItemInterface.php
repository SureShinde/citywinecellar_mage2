<?php
/**
 *  Copyright © Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Api\Data;

interface ReportPanelItemInterface
{
    /**#@+
     * @const
     */
    const ITEM_ID = 'id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const ACTION = 'action';
    const IS_VISIBLE = 'is_visible';
    const SORT_ORDER = 'sort_order';
    /**#@- */

    /**
     * Get Id
     *
     * @return string
     * */
    public function getId();

    /**
     * Set Id
     *
     * @param string $value
     * @return $this
     * */
    public function setId($value);

    /**
     * Get title
     *
     * @return string
     * */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $value
     * @return $this
     * */
    public function setTitle($value);

    /**
     * Get Description
     *
     * @return string
     * */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $value
     * @return $this
     * */
    public function setDescription($value);

    /**
     * Get Action
     *
     * @return string
     * */
    public function getAction();

    /**
     * Set Action
     *
     * @param string $value
     * @return $this
     * */
    public function setAction($value);

    /**
     * Get Is Visible
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * */
    public function getIsVisible();

    /**
     * Set Is Visible
     *
     * @param bool $value
     * @return $this
     * */
    public function setIsViSible($value);

    /**
     * Get Sort Order
     *
     * @return int
     * */
    public function getSortOrder();

    /**
     * Set Sort Order
     *
     * @param int $value
     * @return $this
     * */
    public function setSortOrder($value);
}
