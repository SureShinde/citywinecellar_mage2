<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel\Rewards;

use Amasty\Rewards\Model\ResourceModel\Rewards as RewardsResource;
use Amasty\Rewards\Model\Rewards;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registryManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_registryManager = $registry;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Initialize resource model for collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Rewards::class, RewardsResource::class);
    }

    /**
     * Add filtration by customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerIdFilter($customerId)
    {
        $this->getSelect()->where(
            'main_table.customer_id = ?',
            $customerId
        );

        return $this;
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function addExpiration($date)
    {
        $this->getSelect()->joinLeft(
            ['expiration' => $this->getTable(\Amasty\Rewards\Model\ResourceModel\Expiration::TABLE_NAME)],
            'main_table.expiration_id = expiration.entity_id',
            ['days_left' => 'DATEDIFF(expiration.date, \'' . $date . '\')']
        )->order('main_table.id DESC');

        return $this;
    }

    public function addStatistic()
    {
        $this->getSelect()->columns('SUM(amount)');
    }
}
