<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel\Quote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Amasty\Rewards\Helper\Data
     */
    public $rewardsDataHelper;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection,
        \Amasty\Rewards\Helper\Data $rewardsDataHelper,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
    ) {
        $this->rewardsDataHelper = $rewardsDataHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Rewards\Model\Quote::class, \Amasty\Rewards\Model\ResourceModel\Quote::class);
    }
}
