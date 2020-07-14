<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Grid;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;
use Magestore\FulfilSuccess\Service\Locator\BatchServiceInterface;
use Magestore\FulfilSuccess\Service\Locator\UserServiceInterface;


class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $ageField = 'TIMESTAMPDIFF(SECOND, main_table.created_at, NOW())';
    /**
     * @var LocationServiceInterface
     */
    protected $locationService;

    /**
     * @var BatchServiceInterface
     */
    protected $batchService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

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
     * @param BatchServiceInterface $batchService
     * @param UserServiceInterface $userService
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
        BatchServiceInterface $batchService,
        UserServiceInterface $userService,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        $mainTable = 'os_fulfilsuccess_pickrequest',
        $resourceModel = 'Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest'
    )
    {
        $this->locationService = $locationService;
        $this->batchService = $batchService;
        $this->userService = $userService;
        $this->fulfilManagement = $fulfilManagement;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * prepare collection
     *
     * @return array
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFilterFieldMap();

        $this->addFieldToFilter(PickRequestInterface::STATUS, PickRequestInterface::STATUS_PICKING);

        $warehouseId = $this->locationService->getCurrentWarehouseId();
        if ($warehouseId) {
            if ($this->fulfilManagement->isMSIEnable()) {
                $this->addFieldToFilter(PickRequestInterface::SOURCE_CODE, $warehouseId);
            } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
                $this->addFieldToFilter(PickRequestInterface::WAREHOUSE_ID, $warehouseId);
            }
        }

        $batchId = $this->batchService->getCurrentBatchId();
        if ($batchId) {
            $this->addFieldToFilter(PickRequestInterface::BATCH_ID, $batchId);
        }

        $this->getSelect()->join(
            ['order' => $this->getTable('sales_order_grid')],
            'main_table.order_id = order.entity_id',
            [
                'increment_id',
                'shipping_name',
                'customer_email',
                'purchased_at' => 'order.created_at',
                'base_grand_total',
            ]);

        $this->getSelect()->columns([
            'age' => new \Zend_Db_Expr($this->ageField)
        ]);

        return $this;
    }


    /**
     * Add Filter fiel map for pack request collection
     *
     * @return $this
     */
    public function addFilterFieldMap()
    {
        return $this->addFilterToMap(
            PickRequestInterface::STATUS,
            new \Zend_Db_Expr('main_table.' . PickRequestInterface::STATUS)
        )->addFilterToMap(
            PickRequestInterface::WAREHOUSE_ID,
            new \Zend_Db_Expr('main_table.' . PickRequestInterface::WAREHOUSE_ID)
        )->addFilterToMap(
            PickRequestInterface::USER_ID,
            new \Zend_Db_Expr('main_table.' . PickRequestInterface::USER_ID)
        )->addFilterToMap(
            'age',
            new \Zend_Db_Expr($this->ageField)
        );
    }

    /**
     * @return $data
     */
    public function getData()
    {
        $data = parent::getData();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $timeZone = $om->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $requestInterface = $om->get('Magento\Framework\App\RequestInterface');
        if (($requestInterface->getActionName() == 'gridToCsv') || ($requestInterface->getActionName() == 'gridToXml')) {
            foreach ($data as &$item) {
                $item['purchased_at'] = $timeZone->date($item['purchased_at'])->format('Y-m-d H:i:s');
            }
        }
        return $data;
    }
}