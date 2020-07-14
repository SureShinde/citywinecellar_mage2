<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Class Warehouse
 * @package Magestore\ReportSuccess\Model\Source\Adminhtml
 */
class Warehouse implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * Warehouse constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
    )
    {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->reportManagement = $reportManagement;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->reportManagement->isMSIEnable()) {
            $options[] = array('value' => ' ', 'label' => __('All Sources'));
            $sourcesList = $this->getSourceList();
            foreach ($sourcesList as $source) {
                $options = array_merge(
                    $options,
                    [[
                        'value' => $source->getSourceCode(),
                        'label' => $source->getName() . ' (' . $source->getSourceCode() . ')'
                    ]]
                );
            }
            return $options;
        }

        if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            $options[] = array('value' => ' ', 'label' => __('All Warehouses'));
            $sourceWarehouse = $this->objectManager->create(
                'Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection'
            )->setOrder(
                'warehouse_name',
                \Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection::SORT_ORDER_ASC
            );
            $this->objectManager->get('Magestore\InventorySuccess\Api\Permission\PermissionManagementInterface')
                ->filterPermission($sourceWarehouse, 'Magestore_InventorySuccess::warehouse_view');
            foreach ($sourceWarehouse as $item) {
                $label = $item->getData('warehouse_name')
                    ? $item->getData('warehouse_name') . '(' . $item->getData('warehouse_code') . ')'
                    : $item->getData('warehouse_id') . '(' . $item->getData('warehouse_code') . ')';
                $options[] = array('value' => $item->getId(), 'label' => $label);
            }
            return $options;
        }
        return array();
    }

    /**
     * @return array
     */
    public function toOptionListArray()
    {
        $options = [];
        $reportManagement = $this->objectManager->get('Magestore\ReportSuccess\Api\ReportManagementInterface');
        if ($reportManagement->isMSIEnable()) {
            $sourcesList = $this->getSourceList();
            foreach ($sourcesList as $source) {
                $options = array_merge(
                    $options,
                    [['value' => $source->getSourceCode(), 'label' => $source->getName()]]
                );
            }
        } else if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            $sourceWarehouse = $this->objectManager->create(
                'Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection'
            )->setOrder(
                'warehouse_name',
                \Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection::SORT_ORDER_ASC
            );
            foreach ($sourceWarehouse as $warehouse) {
                $options[$warehouse->getId()] = $warehouse->getWarehouseName();
            }
        }
        return $options;
    }

    /**
     * @return mixed
     */
    public function getSourceList()
    {
        /**@var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->objectManager->create('Magento\Framework\Api\SortOrderBuilder');
        $sortOrder = $sortOrderBuilder->setField(\Magento\InventoryApi\Api\Data\SourceInterface::NAME)
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();
        /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
        $searchCriteria = $this->objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder')
            ->setSortOrders([$sortOrder])
            ->create();
        $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
        $sourcesSearchResult = $sourceRepository->getList($searchCriteria);
        return $sourcesSearchResult->getItems();
    }
}
