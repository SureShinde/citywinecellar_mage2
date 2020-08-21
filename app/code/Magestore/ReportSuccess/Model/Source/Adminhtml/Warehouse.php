<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Source\Adminhtml;

/**
 * Source - Warehouse model
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
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->reportManagement = $reportManagement;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if ($this->reportManagement->isMSIEnable()) {
            $options[] = ['value' => ' ', 'label' => __('All Sources')];
            $sourcesList = $this->getSourceList();
            foreach ($sourcesList as $source) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
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
        return [];
    }

    /**
     * To Option List Array
     *
     * @return array
     */
    public function toOptionListArray()
    {
        $options = [];
        $reportManagement = $this->objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $sourcesList = $this->getSourceList();
            foreach ($sourcesList as $source) {
                $options[$source->getSourceCode()] = $source->getName();
            }
        }
        return $options;
    }

    /**
     * Get Source List
     *
     * @return mixed
     */
    public function getSourceList()
    {
        /**@var \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->objectManager->create(\Magento\Framework\Api\SortOrderBuilder::class);
        $sortOrder = $sortOrderBuilder->setField(\Magento\InventoryApi\Api\Data\SourceInterface::NAME)
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();
        /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
        $searchCriteria = $this->objectManager->create(\Magento\Framework\Api\SearchCriteriaBuilder::class)
            ->setSortOrders([$sortOrder])
            ->create();
        $sourceRepository = $this->objectManager->get(\Magento\InventoryApi\Api\SourceRepositoryInterface::class);
        $sourcesSearchResult = $sourceRepository->getList($searchCriteria);
        return $sourcesSearchResult->getItems();
    }
}
