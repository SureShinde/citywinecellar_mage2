<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magestore\ReportSuccess\Api\ReportManagementInterface;

/**
 * Class Columns
 * @package Magestore\ReportSuccess\Ui\Component\Columns
 */
class Columns extends \Magento\Ui\Component\Listing\Columns
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\ReportSuccess\Ui\Component\ColumnFactory|ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var \Magestore\ReportSuccess\Model\Bookmark
     */
    protected $bookmark;

    /**
     * @var ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * Columns constructor.
     * @param ContextInterface $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\ReportSuccess\Ui\Component\ColumnFactory $columnFactory
     * @param ReportManagementInterface $reportManagement
     * @param \Magestore\ReportSuccess\Model\Bookmark $bookmark
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\ReportSuccess\Ui\Component\ColumnFactory $columnFactory,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement,
        \Magestore\ReportSuccess\Model\Bookmark $bookmark,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $components, $data);
        $this->objectManager = $objectManager;
        $this->columnFactory = $columnFactory;
        $this->bookmark = $bookmark;
        $this->reportManagement = $reportManagement;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepare()
    {

        $locations = $this->bookmark->getLocations();
        if ($this->reportManagement->isMSIEnable()) {
            $warehouse = $this->objectManager->create('Magento\Inventory\Model\Source');
            $warehouseName = __('All Sources');
            $warehouseList = $this->getSourceList($locations);
        }else{
            $warehouse = $this->objectManager->create('Magestore\InventorySuccess\Model\Warehouse');
            $warehouseName = __('All Warehouses');

            /** @var \Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection $warehouseList */
            $warehouseList = $this->objectManager
                ->create('Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection');
            $warehouseList->addFieldToFilter('warehouse_id', ['in' => $locations]);
            $warehouseList->setOrder(
                'warehouse_name',
                \Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection::SORT_ORDER_ASC
            );
        }
        if (in_array(' ', $locations)) {
            /** @var \Magestore\InventorySuccess\Model\Warehouse $warehouse */
            $warehouse->setId('_');
            $warehouse->setWarehouseName($warehouseName);
            $column = $this->columnFactory->create($warehouse, $this->getContext());
            $column->prepare();
            $this->addComponent('loc_' . $warehouse->getId(), $column);
        }

        foreach ($warehouseList as $warehouse) {
            $column = $this->columnFactory->create($warehouse, $this->getContext());
            $column->prepare();
            $warehouseId = $this->reportManagement->isMSIEnable() ? $warehouse->getSourceCode() : $warehouse->getId();
            $this->addComponent('loc_' . $warehouseId, $column);
        }
        parent::prepare();

    }

    /**
     * @param $locations
     * @return mixed
     */
    public function getSourceList($locations){
        /**@var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->objectManager->create('Magento\Framework\Api\SortOrderBuilder');
        $sortOrder = $sortOrderBuilder->setField(\Magento\InventoryApi\Api\Data\SourceInterface::NAME)
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();
        /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
        $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
        $searchCriteria = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder')
            ->addFilter('source_code', $locations, 'in')
            ->setSortOrders([$sortOrder])
            ->create();
        $sourcesSearchResult = $sourceRepository->getList($searchCriteria);
        return $sourcesSearchResult->getItems();
    }
}
