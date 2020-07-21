<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin\Customer\Model\ResourceModel\CustomerGrid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    const ALIAS_FIELD_NAME = 'amrewardpoints';

    const ALIAS_TABLE_NAME = 'amrewards';

    const TABLE_FIELD = 'points_left';

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $aliasForTableAgain = self::ALIAS_TABLE_NAME . '_again';
        $this->getSelect()->joinLeft(
            [self::ALIAS_TABLE_NAME => $this->getTable("amasty_rewards_rewards")],
            'main_table.entity_id = ' . self::ALIAS_TABLE_NAME . '.customer_id',
            [self::ALIAS_FIELD_NAME => self::TABLE_FIELD]
        )->joinLeft(
            [$aliasForTableAgain => $this->getTable("amasty_rewards_rewards")],
            $aliasForTableAgain.'.customer_id = ' . self::ALIAS_TABLE_NAME . '.customer_id AND '
            . self::ALIAS_TABLE_NAME . '.id < ' . $aliasForTableAgain . '.id',
            []
        )->where($aliasForTableAgain . '.id IS NULL');

        return $this;
    }

    /**
     * @param string $field
     * @param null $condition
     *
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === self::ALIAS_FIELD_NAME) {
            if (($field === self::ALIAS_FIELD_NAME && isset($condition['gteq']) && $condition['gteq'] <= 0)
                || ($field === self::ALIAS_FIELD_NAME && isset($condition['lteq']))
            ) {
                $condition = $this->_translateCondition($field, $condition);
                $condition = $this->replaceCondition($condition);
                $where = self::ALIAS_TABLE_NAME . '.' . $condition . ' OR ' . self::ALIAS_TABLE_NAME . '.'
                    . self::TABLE_FIELD . ' IS NULL';
                $this->getSelect()->where($where);

                return $this;
            } else {
                $condition = $this->replaceCondition($condition);
                $field = self::ALIAS_TABLE_NAME . '.' . self::TABLE_FIELD;
            }
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param $condition
     *
     * @return string
     */
    public function replaceCondition($condition)
    {
        return str_replace(self::ALIAS_FIELD_NAME, self::TABLE_FIELD, $condition);
    }
}
