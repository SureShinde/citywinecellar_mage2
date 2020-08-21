<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher;

/**
 * Giftvoucher resource collection
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'giftvoucher_id';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher $resource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\DB\Adapter\AdapterInterface  $connection = null
    ) {
        $this->_date = $date;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\Giftvoucher\Model\Giftvoucher::class,
            \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher::class
        );
    }

    /**
     * Add Item Filter
     *
     * @param int $quoteItemId
     * @param bool $isUseItemId
     *
     * @return $this
     */
    public function addItemFilter($quoteItemId, $isUseItemId = false)
    {
        $filterField = 'quote_item_id';
        if ($isUseItemId) {
            $filterField = 'order_item_id';
        }
        if ($this->hasFlag('add_item_filer') && $this->getFlag('add_item_filer')) {
            return $this;
        }
        $this->setFlag('add_item_filer', true);

        $this->getSelect()->joinLeft(
            ['history' => $this->getTable('giftvoucher_history')],
            'main_table.giftvoucher_id = history.giftvoucher_id',
            [$filterField]
        )->where('history.'. $filterField .' = ?', $quoteItemId)
        ->where('history.action = ?', \Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE);

        return $this;
    }

    /**
     * Add Expire After Days Filter
     *
     * @param int|string $dayBefore
     *
     * @return $this
     * @throws \Zend_Date_Exception
     */
    public function addExpireAfterDaysFilter($dayBefore)
    {
        $date = $this->_date->gmtDate();
        $zendDate = new \Zend_Date($date);
        $dayAfter = $zendDate->addDay($dayBefore)->toString('YYYY-MM-dd');
        $this->getSelect()->where('date(expired_at) = ?', $dayAfter);
        return $this;
    }
}
