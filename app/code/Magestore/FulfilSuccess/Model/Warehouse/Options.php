<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Warehouse;


abstract class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $showAnonymousRow = false;

    /**
     * @var \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface
     */
    protected $locationService;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Options constructor.
     * @param \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface $locationService
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface $locationService,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->locationService = $locationService;
        $this->objectManager = $objectManager;
        $this->fulfilManagement = $fulfilManagement;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     *
     * @return \Magestore\FulfilSuccess\Model\Warehouse\Options
     */
    public function showDummyRow()
    {
        $this->showAnonymousRow = true;
        return $this;
    }

    /**
     * Get permission of current task
     *
     * @return string
     */
    abstract public function getPermission();

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        $allowedIds = $this->locationService->getAllowedWarehouses($this->getPermission());
        if ($this->fulfilManagement->isMSIEnable()) {
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('source_code', $allowedIds, 'in')
                ->create();
            $sourcesSearchResult = $sourceRepository->getList($searchCriteria);
            $sourcesList = $sourcesSearchResult->getItems();
            if (null == $this->options) {
                if ($this->showAnonymousRow) {
                    $this->options = [['value' => "", 'label' => __('Select Warehouse')]];
                } else {
                    $this->options = [];
                }
                foreach ($sourcesList as $source) {
                    $this->options = array_merge(
                        $this->options,
                        [['value' => $source->getSourceCode(), 'label' => $source->getName()]]
                    );
                }
            }
        } else if ($this->fulfilManagement->isInventorySuccessEnable()) {
            $collection = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Collection')
                ->addFieldToFilter('warehouse_id', ['in' => $allowedIds]);
            if (null == $this->options) {
                if ($this->showAnonymousRow) {
                    $this->options = [['value' => 0, 'label' => __('Select Warehouse')]];
                } else {
                    $this->options = [];
                }
                $this->options = array_merge($this->options, $collection->toOptionArray());
            }
        }

        return $this->options;
    }
}