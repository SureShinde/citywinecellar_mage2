<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Class LocationCode
 * @package Magestore\ReportSuccess\Model\Source\Adminhtml
 */
class LocationCode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * LocationCode constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->objectManager = $objectManager;
        $this->reportManagement = $reportManagement;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $resourceList = $this->toOptionListArray();
        foreach ($resourceList as $resourceValue => $resourceLabel) {
            $options[] = array('value' => $resourceValue, 'label' => $resourceLabel);
        }
        return $options;
    }

    /**
     * @return array
     */
    public function toOptionListArray()
    {
        $options = [];
        if ($this->reportManagement->isMSIEnable()) {
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $sourceList = $sourceRepository->getList()->getItems();
            foreach ($sourceList as $source) {
                $options[$source->getSourceCode()] = $source->getName();
            }
            asort($options);
            $options = array_merge(['all' => __('All Sources')], $options);
        } else if ($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            $sourceWarehouse = $this->objectManager->create('Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection');
            $options['all'] = __('All Warehouses');
            foreach ($sourceWarehouse as $warehouse) {
                $options[$warehouse->getWarehouseCode()] = $warehouse->getWarehouseName();
            }
        }
        return $options;
    }
}
