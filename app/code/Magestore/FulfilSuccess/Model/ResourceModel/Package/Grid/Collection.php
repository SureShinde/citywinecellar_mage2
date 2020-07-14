<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\Package\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\FulfilSuccess\Api\Data\PackageInterface;
use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;


class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var LocationServiceInterface
     */
    protected $locationService;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param LocationServiceInterface $locationService
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        LocationServiceInterface $locationService,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        $mainTable = 'os_fulfilsuccess_package',
        $resourceModel = 'Magestore\FulfilSuccess\Model\ResourceModel\Package\Package'
    )
    {
        $this->locationService = $locationService;
        $this->fulfilManagement = $fulfilManagement;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $isMSIEnable = $this->fulfilManagement->isMSIEnable();
        $isInventorySuccessEnable = $this->fulfilManagement->isInventorySuccessEnable();
        $this->addFilterToMap(
            'created_at',
            'sales_shipment_grid.created_at'
        );
        if ($isMSIEnable) {
            $this->addFilterToMap(
                PackageInterface::WAREHOUSE_ID,
                'main_table.' . PackageInterface::SOURCE_CODE
            );
        } else if ($isInventorySuccessEnable) {
            $this->addFilterToMap(
                PackageInterface::WAREHOUSE_ID,
                'main_table.' . PackageInterface::WAREHOUSE_ID
            );
        }
        /* warehouse filter */
        $warehouseId = $this->locationService->getCurrentWarehouseId();
        if ($warehouseId) {
            if ($isMSIEnable) {
                $this->addFieldToFilter(PackageInterface::SOURCE_CODE, $warehouseId);
            } else if ($isInventorySuccessEnable) {
                $this->addFieldToFilter(PackageInterface::WAREHOUSE_ID, $warehouseId);
            }
        }

        $this->getSelect()->joinLeft(
            ['sales_shipment_track' => $this->getTable('sales_shipment_track')],
            'main_table.track_id = sales_shipment_track.entity_id',
            ['*']
        );
        $this->getSelect()->joinLeft(
            ['sales_shipment_grid' => $this->getTable('sales_shipment_grid')],
            'main_table.shipment_id = sales_shipment_grid.entity_id',
            ['*']
        );
        $this->getSelect()->joinLeft(
            ['sales_order' => $this->getTable('sales_order')],
            'sales_shipment_grid.order_id = sales_order.entity_id',
            ['shipping_method']
        );
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);

        return $this;
    }

    /**
     * rewrite add field to filters from collection
     *
     * @return array
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'actions') {
            $field = 'sales_shipment_track.track_number';
            $condition = array('eq' => trim($condition['like'], ' %'));
        }
        return parent::addFieldToFilter($field, $condition);
    }

    public function getData()
    {
        $data = parent::getData();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $timeZone = $om->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $requestInterface = $om->get('Magento\Framework\App\RequestInterface');
        if (($requestInterface->getActionName() == 'gridToCsv') || ($requestInterface->getActionName() == 'gridToXml')) {
            foreach ($data as &$item) {
                $item['created_at'] = $timeZone->date($item['created_at'])->format('m-d-Y H:i:s');
            }
        }
        return $data;
    }

}