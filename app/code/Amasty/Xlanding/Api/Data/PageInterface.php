<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Api\Data;

interface PageInterface
{
    const LANDING_PAGE_ID = 'page_id';
    const LANDING_TITLE = 'title';
    const LANDING_IDENTIFIER = 'identifier';
    const LANDING_PAGE_LAYOUT = 'page_layout';
    const LAYOUT_COLUMNS_COUNT = 'layout_columns_count';
    const LAYOUT_INCLUDE_NAVIGATION = 'layout_include_navigation';
    const LAYOUT_HEADING = 'layout_heading';
    const LAYOUT_FILE = 'layout_file';
    const LAYOUT_FILE_ALT = 'layout_file_alt';
    const LAYOUT_TOP_DESCRIPTION = 'layout_top_description';
    const LAYOUT_BOTTOM_DESCRIPTION = 'layout_bottom_description';
    const LAYOUT_STATIC_TOP = 'layout_static_top';
    const LAYOUT_STATIC_BOTTOM = 'layout_static_bottom';
    const DEFAULT_SORT_BY = 'default_sort_by';
    const LANDING_CREATION_TIME = 'creation_time';
    const LANDING_UPDATE_TIME = 'update_time';
    const LANDING_IS_ACTIVE = 'is_active';
    const LANDING_SORT_ORDER = 'sort_order';
    const LANDING_LAYOUT_UPDATE_XML = 'layout_update_xml';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const META_DATA = 'meta_data';
    const IS_CATEGORY_DYNAMIC = 'amlanding_is_dynamic';
    const DYNAMIC_CATEGORY_PAGE_ID = 'amlanding_page_id';
    const DYNAMIC_CATEGORY_ID = 'dynamic_category_id';

    /**
     * @return int
     */
    public function getPageId();

    /**
     * @param int $pageId
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setPageId($pageId);

    /**
     * @return string|null
     */
    public function getTitle();

    /**
     * @param string|null $title
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setTitle($title);

    /**
     * @return string|null
     */
    public function getIdentifier();

    /**
     * @param string|null $identifier
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setIdentifier($identifier);

    /**
     * @return string|null
     */
    public function getPageLayout();

    /**
     * @param string|null $pageLayout
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setPageLayout($pageLayout);

    /**
     * @return string|null
     */
    public function getLayoutColumnsCount();

    /**
     * @param string|null $layoutColumnsCount
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutColumnsCount($layoutColumnsCount);

    /**
     * @return int
     */
    public function getLayoutIncludeNavigation();

    /**
     * @param int $layoutIncludeNavigation
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutIncludeNavigation($layoutIncludeNavigation);

    /**
     * @return string|null
     */
    public function getLayoutHeading();

    /**
     * @param string|null $layoutHeading
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutHeading($layoutHeading);

    /**
     * @return string|null
     */
    public function getLayoutFile();

    /**
     * @param string|null $layoutFile
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutFile($layoutFile);

    /**
     * @return string|null
     */
    public function getLayoutFileAlt();

    /**
     * @param string|null $layoutFileAlt
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutFileAlt($layoutFileAlt);

    /**
     * @return string|null
     */
    public function getLayoutTopDescription();

    /**
     * @param string|null $layoutTopDescription
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutTopDescription($layoutTopDescription);

    /**
     * @return string|null
     */
    public function getLayoutBottomDescription();

    /**
     * @param string|null $layoutBottomDescription
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutBottomDescription($layoutBottomDescription);

    /**
     * @return string|null
     */
    public function getLayoutStaticTop();

    /**
     * @param string|null $layoutStaticTop
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutStaticTop($layoutStaticTop);

    /**
     * @return string|null
     */
    public function getLayoutStaticBottom();

    /**
     * @param string|null $layoutStaticBottom
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutStaticBottom($layoutStaticBottom);

    /**
     * @return string|null
     */
    public function getDefaultSortBy();

    /**
     * @param string|null $defaultSortBy
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setDefaultSortBy($defaultSortBy);

    /**
     * @return string
     */
    public function getCreationTime();

    /**
     * @param string $creationTime
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setCreationTime($creationTime);

    /**
     * @return string
     */
    public function getUpdateTime();

    /**
     * @param string $updateTime
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param int $isActive
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string|null
     */
    public function getLayoutUpdateXml();

    /**
     * @param string|null $layoutUpdateXml
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml);

    /**
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * @param string|null $conditionsSerialized
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @return string|null
     */
    public function getMetaData();

    /**
     * @param string|null $metaData
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setMetaData($metaData);

    /**
     * @return string
     */
    public function getDynamicCategoryId();

    /**
     * @param string $id
     *
     * @return \Amasty\Xlanding\Api\Data\PageInterface
     */
    public function setDynamicCategoryId($id);
}
