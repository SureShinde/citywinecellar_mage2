<?php

namespace Laconica\Feed\Model\Export;

use \Magento\Store\Model\ScopeInterface;

class Product extends \Amasty\Feed\Model\Export\Product
{
    const REPLACE_ENABLED_XML_PATH = 'feed/general/enabled';
    const INVALID_CATEGORIES_XML_PATH = 'feed/general/invalid_category';
    const FILTER_LIST_XML_PATH = 'feed/general/filter_list';
    const STORE_IGNORE_CATEGORY_XML_PATH = 'feed/general/ignore_category';

    protected function _createExportRow($attributes, $dataRow, $childDataRow, $basicTypes, $customTypes, &$exportRow)
    {
        parent::_createExportRow($attributes, $dataRow, $childDataRow, $basicTypes, $customTypes, $exportRow);

        $storeId = (isset($dataRow['store_id'])) ? $dataRow['store_id'] : null;

        if (!$this->isEnabled($storeId)) {
            return;
        }

        $postfix = count($childDataRow) > 0 ? '|parent' : '';
        $productNameCode = 'product|name' . $postfix;
        $categoryNameCode = 'category|category' . $postfix;
        $categoryIdsCode = 'advanced|category_ids' . $postfix;
        $productCategories = (isset($exportRow[$categoryIdsCode]) && $exportRow[$categoryIdsCode]) ? explode(',', $exportRow[$categoryIdsCode]) : [];
        $invalidCategories = $this->getFeedReplaceInvalidCategories($storeId);

        if (isset($exportRow[$productNameCode], $exportRow[$categoryNameCode]) && $productCategories && $this->isCategoryValid($productCategories, $invalidCategories)) {
            $categoryName = trim($this->getCategoryName($dataRow));
            $productName = trim($exportRow[$productNameCode]);
            $filterList = trim($this->getFilterList($storeId));
            $exportRow[$productNameCode] = preg_replace('/^(.*) (\d.*(' . $filterList . '))$/', '$1 ' . $categoryName . ' $2', $productName);
        }
    }

    /**
     * Return category name from it's hierarchy tree string
     *
     * @param array $dataRow
     * @return mixed|string
     */
    private function getCategoryName($dataRow)
    {
        if (!isset($dataRow['_category'], $dataRow['store_id'])) {
            return '';
        }

        $storeId = $dataRow['store_id'];
        $ignoreCategories = $this->getStoreIgnoreCategories($storeId);
        $searchData = ($ignoreCategories) ? array_map('trim', explode(',', $ignoreCategories)) : [];
        $replaceData = [];

        foreach ($searchData as $item) {
            array_push($replaceData, '');
        }

        $categoriesString = str_replace($searchData, $replaceData, $dataRow['_category']);
        $storeCategories = explode(",", $categoriesString);
        $storeIndex = (intval($storeId) > 0) ? intval($storeId) - 1 : 0;
        $categories = ($storeCategories[$storeIndex]) ? explode("/", $storeCategories[$storeIndex]) : [];

        return (!empty($categories)) ? array_shift($categories) : '';
    }

    /**
     * Check is category valid for title replace
     *
     * @param $productCategories
     * @param $invalidCategories
     * @return bool
     */
    private function isCategoryValid($productCategories, $invalidCategories)
    {
        foreach ($productCategories as $category) {
            if (in_array($category, $invalidCategories)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns invalid categories list
     *
     * @param null $storeId
     * @return array
     */
    private function getFeedReplaceInvalidCategories($storeId = null)
    {
        $categories = $this->_scopeConfig->getValue(self::INVALID_CATEGORIES_XML_PATH, ScopeInterface::SCOPE_WEBSITE, $storeId);
        return ($categories) ? explode(",", $categories) : [];
    }

    /**
     * Check is modification enabled
     *
     * @param null $storeId
     * @return mixed
     */
    private function isEnabled($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::REPLACE_ENABLED_XML_PATH, ScopeInterface::SCOPE_WEBSITE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    private function getFilterList($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::FILTER_LIST_XML_PATH, ScopeInterface::SCOPE_WEBSITE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    private function getStoreIgnoreCategories($storeId = null)
    {
        return $this->_scopeConfig->getValue(self::STORE_IGNORE_CATEGORY_XML_PATH, ScopeInterface::SCOPE_WEBSITE, $storeId);
    }
}