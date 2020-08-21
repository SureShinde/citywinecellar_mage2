<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Location code model
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
    ) {
        $this->objectManager = $objectManager;
        $this->reportManagement = $reportManagement;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        $resourceList = $this->toOptionListArray();
        foreach ($resourceList as $resourceValue => $resourceLabel) {
            $options[] = ['value' => $resourceValue, 'label' => $resourceLabel];
        }
        return $options;
    }

    /**
     * To Option List Array
     *
     * @return array
     */
    public function toOptionListArray()
    {
        $options = [];
        if ($this->reportManagement->isMSIEnable()) {
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->objectManager->get(\Magento\InventoryApi\Api\SourceRepositoryInterface::class);
            $sourceList = $sourceRepository->getList()->getItems();
            foreach ($sourceList as $source) {
                $options[$source->getSourceCode()] = $source->getName();
            }
            asort($options);
            $options = array_merge(['all' => __('All Sources')], $options);
        }
        return $options;
    }
}
