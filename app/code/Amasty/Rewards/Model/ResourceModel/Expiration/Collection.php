<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel\Expiration;

use Magento\Customer\Controller\RegistryConstants as RegistryConstants;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->registry = $registry;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Initialize resource model for collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Amasty\Rewards\Model\Expiration::class, \Amasty\Rewards\Model\ResourceModel\Expiration::class);
    }

    /**
     * @param int|null $customerId
     * @param bool $isGrouped
     */
    public function getPointsByCustomerId($customerId = null, $isGrouped = true)
    {
        if ($customerId == null) {
            $customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        }

        $this->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['entity_id', 'amount', 'date'])
            ->order(['ISNULL(date)', 'date ASC'])
            ->where('customer_id = ?', $customerId);

        if ($isGrouped) {
            $this->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(['entity_id', 'amount' => 'SUM(amount)', 'date'])
                ->group(['date', 'customer_id']);
        }
    }
}
