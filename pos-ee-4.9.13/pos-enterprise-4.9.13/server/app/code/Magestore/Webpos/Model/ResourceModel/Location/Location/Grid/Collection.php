<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Location\Location\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * class \Magestore\Webpos\Model\ResourceModel\Location\Location\Grid\Collection
 *
 * Web POS Staff Grid Collection resource model
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Collection extends SearchResult
{
    /**
     * @return $this|void
     */
    protected function _initSelect()
    {

        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->columns(
                [
                    'address' => new \Zend_Db_Expr("CONCAT_WS(', ',street, city, region, country, postcode)")
                ]);

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (is_array($field)) {
            $conditions = [];
            foreach ($field as $key => $value) {
                $conditions[] = $this->_translateCondition($value, isset($condition[$key]) ? $condition[$key] : null);
            }

            $resultCondition = '(' . implode(') ' . \Magento\Framework\DB\Select::SQL_OR . ' (', $conditions) . ')';
        } else {
            $resultCondition = $this->_translateCondition($field, $condition);
        }

        if($field != 'address') {
            $this->_select->where($resultCondition, null, \Magento\Framework\DB\Select::TYPE_CONDITION);
        } else {
            $this->_select->having($resultCondition, null, \Magento\Framework\DB\Select::TYPE_CONDITION);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        if (!count($this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP))) {
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            $countSelect->columns(
                [
                    'address' => new \Zend_Db_Expr("CONCAT_WS(', ',street, city, region, country, postcode)")
                ]);
            return $countSelect;
        }

        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT ".implode(", ", $group).")")));
        return $countSelect;
    }
}