<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel;

class Rewards extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'amasty_rewards_rewards';

    /**#@+
     * Statistics columns
     */
    const REWARDED = 'rewarded';

    const REDEEMED = 'redeemed';

    const EXPIRED = 'expired';

    const PERIOD = 'period';

    const BALANCE = 'balance';
    /**#@-*/

    /**#@+
     * Period unit for graph
     */
    const HOUR = 1;

    const DAY = 2;
    /**#@-*/

    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_rewards_rewards', 'id');
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();

        $rewardTable = $this->getTable('amasty_rewards_rewards');

        $select = $connection->select()
            ->from(
                $rewardTable,
                'SUM(amount)'
            )
            ->where('customer_id = :customer_id');

        $pointsLeft = $connection->fetchOne(
            $select,
            [
                ':customer_id' => $object->getCustomerId()
            ]
        );

        $this->getConnection()->update(
            $this->getTable('amasty_rewards_rewards'),
            ['points_left' => $pointsLeft],
            ['id = ?' => $object->getId()]
        );

        return parent::_afterSave($object);
    }

    /**
     * @param int $customerId
     *
     * @return float
     */
    public function loadPointsByCustomerId($customerId)
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getMainTable(),
                'SUM(amount)'
            )->where('customer_id=:customer_id');
        $result = $this->getConnection()->fetchOne(
            $select,
            [
                'customer_id' => $customerId
            ]
        );

        if ($result === null) {
            return 0;
        }

        return $result;
    }

    /**
     * @param int $customerId
     *
     * @return array
     */
    public function getStatistic($customerId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('amasty_rewards_rewards'),
            [
                self::REWARDED => 'SUM(IF(amount >= 0, amount, 0))',
                self::REDEEMED => 'ABS(SUM(IF(amount < 0 AND action != \''
                    . \Amasty\Rewards\Helper\Data::REWARDS_EXPIRED_ACTION . '\', amount, 0)))',
                self::EXPIRED  => 'ABS(SUM(IF(amount < 0 AND action = \''
                    . \Amasty\Rewards\Helper\Data::REWARDS_EXPIRED_ACTION . '\', amount, 0)))',
            ]
        )->where(
            'customer_id = ?',
            (int)$customerId
        );

        $result = $this->getConnection()->fetchRow($select);
        $result[self::BALANCE] = round($result[self::REWARDED] - $result[self::REDEEMED] - $result[self::EXPIRED], 2);

        return $result;
    }

    /**
     * @param int|null $customerId
     *
     * @return array
     */
    public function getRewards($customerId = null)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $select = $connection->select()
            ->from(['main_table' => $table])
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['customer_id', 'amount' => 'points_left'])
            ->joinLeft(
                ['rewards_table' => $table],
                'main_table.customer_id = rewards_table.customer_id AND main_table.id < rewards_table.id',
                null
            )->where('rewards_table.`id` is NULL');

        if ($customerId) {
            $select->where('main_table.`customer_id` = ?', $customerId);
        }

        return $connection->fetchAll($select);
    }

    /**
     * Set filter to load statistics data
     *
     * @param int|null $website
     * @param int|null $customerGroup
     * @param string|null $fromDate
     * @param string|null $toDate
     *
     * @return \Magento\Framework\DB\Select
     */
    public function addParamsFilter($website = null, $customerGroup = null, $fromDate = null, $toDate = null)
    {
        $table = $this->getMainTable();
        $select = $this->getConnection()->select()
            ->from(['main_table' => $table]);
        $joinCustomers = false;

        if ($website) {
            $select->where('customers.website_id =?', $website);
            $joinCustomers = true;
        }

        if ($customerGroup) {
            $select->where('customers.group_id =?', $customerGroup);
            $joinCustomers = true;
        }

        if ($joinCustomers) {
            $select->joinLeft(
                ['customers' => $this->getTable('customer_entity')],
                'main_table.customer_id = customers.entity_id',
                null
            );
        }

        if ($fromDate && $toDate) {
            $select->where('main_table.action_date BETWEEN \'' . $fromDate . '\' AND \'' . $toDate . '\'');
        }

        return $select;
    }

    /**
     * Return total information about rewards by params
     *
     * @param \Magento\Framework\DB\Select $select
     *
     * @return array
     */
    public function getTotalDataByParams($select)
    {
        $connection = $this->getConnection();

        $select
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                self::REWARDED => 'SUM(IF(main_table.amount >= 0, main_table.amount, 0))',
                // TODO: Use action code in database
                self::REDEEMED => 'ABS(SUM(IF(main_table.amount < 0 AND main_table.action != \'Expiration\', '
                    . 'main_table.amount, 0)))',
                // TODO: Use action code in database
                self::EXPIRED => 'ABS(SUM(IF(main_table.amount < 0 AND main_table.action = \'Expiration\', '
                    . 'main_table.amount, 0)))',
            ]);

        return $connection->fetchRow($select);
    }

    /**
     * Return total information about rewards by params
     *
     * @param \Magento\Framework\DB\Select $select
     *
     * @return string
     */
    public function getCustomersCount($select)
    {
        $connection = $this->getConnection();

        $select
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'COUNT(DISTINCT main_table.customer_id)',
            ]);

        return $connection->fetchOne($select);
    }

    /**
     * Return Average Redeemed Points per Order
     *
     * @param \Magento\Framework\DB\Select $select
     *
     * @return float
     */
    public function getAverageOrderAmount($select)
    {
        $connection = $this->getConnection();

        $select
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'summary' => 'ABS(SUM(main_table.amount))',
                'count_order' => 'COUNT(main_table.amount)'
            ])
            ->where('main_table.action = ?', 'Order Paid') // TODO: Use action code in database
            ->group('main_table.action');

        $result = $connection->fetchRow($select);

        if (isset($result['count_order']) && $result['count_order']) {
            return round($result['summary'] / $result['count_order'], 2);
        }

        return 0;
    }

    /**
     * Return Average Redeemed Points per Order
     *
     * @param \Magento\Framework\DB\Select $select
     * @param int $periodUnit
     *
     * @return array
     */
    public function getGraphData($select, $periodUnit = self::DAY)
    {
        $connection = $this->getConnection();

        if ($periodUnit == self::HOUR) {
            $timeFormat = '%Y-%m-%d %H:00';
        } else {
            $timeFormat = '%Y-%m-%d';
        }

        $select
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                self::PERIOD => 'DATE_FORMAT(main_table.action_date, \'' . $timeFormat . '\')',
                self::REWARDED => 'SUM(IF(main_table.amount >= 0, main_table.amount, 0))',
                // TODO: Use action code in database
                self::REDEEMED => 'ABS(SUM(IF(main_table.amount < 0 AND main_table.action != \'Expiration\', '
                    . 'main_table.amount, 0)))'
            ])
            ->group('DATE_FORMAT(main_table.action_date, \'' . $timeFormat . '\')');

        return $connection->fetchAll($select);
    }
}
