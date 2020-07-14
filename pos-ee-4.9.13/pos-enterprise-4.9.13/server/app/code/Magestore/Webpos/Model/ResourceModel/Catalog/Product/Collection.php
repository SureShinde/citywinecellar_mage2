<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magestore\Webpos\Model\ResourceModel\Catalog\Product;


/**
 * Product collection
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @method \Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface getResource()
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const VISIBLE_ON_WEBPOS = 1;

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Catalog\Product', 'Magento\Catalog\Model\ResourceModel\Product');
        $this->_initTables();
    }

    /**
     * filter product collection that visible on webpos
     * @return \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection
     */
    public function addVisibleFilter()
    {
        $this->addAttributeToFilter([
            /*['attribute' => 'webpos_visible', 'is' => new \Zend_Db_Expr('NULL'), 'left'],*/
            ['attribute' => 'webpos_visible', 'eq' => self::VISIBLE_ON_WEBPOS, 'left'],
        ], '', 'left');
        return $this;
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelect();
            $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
        }
        return intval($this->_totalRecords);
    }
    
    /**
     * Get SQL for get record count without left JOINs
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        if (!count($this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP))) {
            $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT ".implode(", ", $group).")")));
        $select = clone $countSelect;
        $countSelect->reset()->from($select, ['COUNT(*)']);
        return $countSelect;
    }
}
