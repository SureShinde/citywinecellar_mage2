<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\Grid;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;
use Magestore\FulfilSuccess\Service\Locator\UserServiceInterface;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $ageField = 'TIMESTAMPDIFF(SECOND, main_table.created_at, NOW())';

    /**
     * @var LocationServiceInterface
     */
    protected $locationService;

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
        UserServiceInterface $userService,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        $mainTable = 'os_fulfilsuccess_packrequest',
        $resourceModel = 'Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequest'
    )
    {
        $this->locationService = $locationService;
        $this->userService = $userService;
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
        parent::_initSelect();
        $this->addFilterFieldMap();
        $this->addFieldToFilter(
            PackRequestInterface::STATUS,
            ['nin' => [PackRequestInterface::STATUS_PACKED, PackRequestInterface::STATUS_CANCELED]]
        );
        $warehouseId = $this->locationService->getCurrentWarehouseId();
        if ($warehouseId) {
            if ($this->fulfilManagement->isMSIEnable()) {
                $this->addFieldToFilter(PackRequestInterface::SOURCE_CODE, $warehouseId);
            } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
                $this->addFieldToFilter(PackRequestInterface::WAREHOUSE_ID, $warehouseId);
            }
        }

        $this->getSelect()->join(['order' => $this->getTable('sales_order_grid')],
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
            PackRequestInterface::STATUS,
            new \Zend_Db_Expr('main_table.' . PackRequestInterface::STATUS)
        )->addFilterToMap(
            PackRequestInterface::WAREHOUSE_ID,
            new \Zend_Db_Expr('main_table.' . PackRequestInterface::WAREHOUSE_ID)
        )->addFilterToMap(
            PackRequestInterface::USER_ID,
            new \Zend_Db_Expr('main_table.' . PackRequestInterface::USER_ID)
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