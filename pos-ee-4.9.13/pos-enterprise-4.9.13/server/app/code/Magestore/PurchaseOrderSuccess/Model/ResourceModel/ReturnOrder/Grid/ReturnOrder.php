<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface;

class ReturnOrder extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

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
        \Magento\Framework\App\RequestInterface $request,
        $mainTable = 'os_return_order',
        $resourceModel = 'Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder'
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    public function getData()
    {
        $data = parent::getData();
        $isExport = $this->request->getParam('is_export');
        if($isExport) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $supplierList = $om->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Supplier')
                ->getSupplierOptions();
            $warehouseList = $om->get('Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\WarehouseEnable')
                ->getSourcesOptions();
            $statusList = $om->get('Magestore\PurchaseOrderSuccess\Model\ReturnOrder\Option\Status')
                ->getOptionHash();
            foreach ($data as &$item) {
                $item['supplier_id'] = $supplierList[$item['supplier_id']];
                $item['warehouse_id'] = $warehouseList[$item['warehouse_id']];
                $item['status'] = $statusList[$item['status']];
            }
        }
        return $data;
    }
}
