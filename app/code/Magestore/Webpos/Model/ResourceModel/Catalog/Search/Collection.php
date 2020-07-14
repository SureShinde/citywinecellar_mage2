<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Catalog\Search;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Magestore\Webpos\Model\Catalog\Search::class,
            \Magestore\Webpos\Model\ResourceModel\Catalog\Search::class
        );
    }
    
    /**
     * Set store ID for collection
     *
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->getResource()->setStoreId($storeId);
        $this->setMainTable($this->getResource()->getMainTable());
        $this->_reset();
    }
    
    /**
     * {@inheritDoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['e' => $this->getMainTable()]);
        return $this;
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
