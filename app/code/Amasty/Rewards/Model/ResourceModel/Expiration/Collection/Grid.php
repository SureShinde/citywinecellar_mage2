<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel\Expiration\Collection;

class Grid extends \Amasty\Rewards\Model\ResourceModel\Expiration\Collection
{
    /**
     * Initialize db select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getPointsByCustomerId();

        return $this;
    }

    /**
     * Get SQL for get record count.
     * Overridden because parent method does not add row with null date into COUNT.
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
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        $entityColumn = $this->getResource()->getIdFieldName();
        $countSelect->columns(new \Zend_Db_Expr(("COUNT( " . $entityColumn . ")")));

        return $countSelect;
    }
}
