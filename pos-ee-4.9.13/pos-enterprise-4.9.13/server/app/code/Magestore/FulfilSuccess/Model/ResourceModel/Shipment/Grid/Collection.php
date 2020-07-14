<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\Shipment\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Shipment\Grid\Collection
{
    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'sales_shipment_grid',
        $resourceModel = 'Magestore\FulfilSuccess\Model\ResourceModel\Shipment\Shipment'
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->joinLeft(['sales_shipment' => $this->getTable('sales_shipment')],
            'main_table.entity_id = sales_shipment.entity_id',
            [
                'fulfil_status'
            ]);
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);;

        return $this;
    }
}