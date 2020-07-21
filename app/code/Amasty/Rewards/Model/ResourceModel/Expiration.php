<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel;

use Amasty\Rewards\Api\Data\ExpirationDateInterface;
use Amasty\Rewards\Model\ConstantRegistryInterface;

class Expiration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'amasty_rewards_expiration_date';

    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ExpirationDateInterface::ENTITY_ID);
    }

    /**
     * @param string $date
     *
     * @return array
     */
    public function getSumExpirationToDate($date)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $select = $connection->select()
            ->from($table, ['customer_id', 'amount' => 'SUM(amount)'])
            ->group('customer_id')
            ->where('date < ?', $date);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param int $customerId
     *
     * @return string
     */
    public function getNonExpireId($customerId)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $select = $connection->select()
            ->from($table, ['entity_id'])
            ->where('date IS NULL')
            ->where('customer_id = ?', $customerId);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param int $customerId
     *
     * @return array
     */
    public function getClosest($customerId)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $select = $connection->select()
            ->from($table, ['amount' => 'SUM(amount)', 'date'])
            ->group(['date'])
            ->order(['ISNULL(date)', 'date ASC'])
            ->where('customer_id = ?', $customerId);

        return $this->getConnection()->fetchRow($select);
    }

    /**
     * Return customers that subscribed to receiving emails about points expiring.
     *
     * @return array
     */
    public function getAllSubscribedCustomers()
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $select = $connection->select()->from(['main_table' => $table], null)
            ->joinLeft(
                ['customers' => $this->getTable('customer_entity')],
                'main_table.customer_id = customers.entity_id',
                null
            )->joinLeft(
                ['eav' => $this->getTable('eav_attribute')],
                'eav.attribute_code = \'' . ConstantRegistryInterface::NOTIFICATION_EXPIRE . '\'',
                null
            )->joinLeft(
                ['eav_value' => $this->getTable('customer_entity_int')],
                'eav_value.entity_id = customers.entity_id AND eav.attribute_id = eav_value.attribute_id',
                null
            )->distinct()->columns(
                [
                    'customer_id' => 'main_table.customer_id',
                    'store_id' => 'customers.store_id'
                ]
            )->where('eav_value.value = ?', 1);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param string $today
     * @param string $toDate
     * @param int[] $customerIds
     *
     * @return array
     */
    public function getFilteredRows($today, $toDate, $customerIds)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $select = $connection->select()->from(['main_table' => $table], null)
            ->joinLeft(
                ['rewards' => $this->getTable(Rewards::TABLE_NAME)],
                'main_table.entity_id = rewards.expiration_id',
                null
            )->joinLeft(
                ['customers' => $this->getTable('customer_entity')],
                'main_table.customer_id = customers.entity_id',
                null
            )->columns(
                [
                    'days_left' => 'DATEDIFF(main_table.date, \'' . $today . '\')',
                    'points' => 'SUM(main_table.amount)',
                    'customer_id' => 'main_table.customer_id',
                    'store_id' => 'customers.store_id',
                    'expiration_date' => 'main_table.date',
                    'earn_date' => 'rewards.action_date'
                ]
            )->order('main_table.date ASC')->where('main_table.date <= ?', $toDate)->where(
                'main_table.customer_id IN (' . implode(',', $customerIds) . ')'
            )->group(['main_table.date', 'main_table.customer_id']);

        return $this->getConnection()->fetchAll($select);
    }
}
