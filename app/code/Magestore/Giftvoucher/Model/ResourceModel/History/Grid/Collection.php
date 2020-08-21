<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\History\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Giftcode History Grid Collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'giftvoucher_history',
        $resourceModel = \Magestore\Giftvoucher\Model\ResourceModel\History::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        parent::_construct();
        $this->addFilterToMap('status', 'main_table.status');
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->joinLeft(
            ['giftvoucher' => $this->getTable('giftvoucher')],
            'main_table.giftvoucher_id = giftvoucher.giftvoucher_id',
            [
                'gift_code'
            ]
        );
        parent::_initSelect();
        return $this;
    }
}
