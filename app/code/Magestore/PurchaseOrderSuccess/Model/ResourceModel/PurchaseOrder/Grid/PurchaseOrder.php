<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Grid;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type as PurchaseOrderType;

/**
 * Class PurchaseOrder
 *
 * Purchase order grid
 */
class PurchaseOrder extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * PurchaseOrder constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\App\RequestInterface $request,
        $mainTable = 'os_purchase_order',
        $resourceModel = \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder::class
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        $isExport = $this->request->getParam('is_export');
        if ($isExport) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $supplierList = $om->get(\Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Supplier::class)
                ->getSupplierOptions();
            $statusList = $om->get(\Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status::class)
                ->getOptionHash();
            $metadataProvider = $om->get(\Magento\Ui\Model\Export\MetadataProvider::class);
            if (!method_exists($metadataProvider, 'getColumnOptions')) {
                foreach ($data as &$item) {
                    $item['supplier_id'] = $supplierList[$item['supplier_id']];
                    $item['status'] = $statusList[$item['status']];
                }
            }
        }
        return $data;
    }

    /**
     * Init select
     *
     * @return $this|PurchaseOrder|void
     */
    protected function _initSelect()
    {
        $supplierId = $this->request->getParam('supplier_id');
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->where(PurchaseOrderInterface::TYPE . ' = ?', $this->getFilterType());
        if ($supplierId) {
            $this->getSelect()->where('main_table.' . PurchaseOrderInterface::SUPPLIER_ID . ' = ?', $supplierId);
        }
        return $this;
    }

    /**
     * Get filter type
     *
     * @return int
     */
    public function getFilterType()
    {
        return PurchaseOrderType::TYPE_PURCHASE_ORDER;
    }
}
