<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\TransferStock\Model\MultiSourceInventory;

/**
 * Class SourceManagement
 * @package Magestore\TransferStock\Model\MultiSourceInventory
 */
class SourceManagement implements \Magestore\TransferStock\Api\MultiSourceInventory\SourceManagementInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * SourceManagement constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    )
    {
        $this->objectManager = $objectManager;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param string $sku
     * @param array $sourceCodes
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function getSourceItemsMap($sku, $sourceCodes)
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter(\Magento\Catalog\Api\Data\ProductInterface::SKU, $sku)
            ->addFilter('source_code', $sourceCodes, 'in')
            ->create();
        /** @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository */
        $sourceItemRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceItemRepositoryInterface');
        $sourceItems = $sourceItemRepository->getList($searchCriteria)->getItems();
        $sourceItemsMap = [];
        if ($sourceItems) {
            foreach ($sourceItems as $sourceItem) {
                $sourceItemsMap[$sourceItem->getSourceCode()] = $sourceItem;
            }
        }
        return $sourceItemsMap;
    }

    /**
     * @inheritDoc
     */
    public function getListProductInSource($sourceCode, $skus = []) {
        if(count($skus)) {
            $searchCriteria = $this->searchCriteriaBuilderFactory
                ->create()
                ->addFilter('source_code', $sourceCode)
                ->addFilter(\Magento\Catalog\Api\Data\ProductInterface::SKU, $skus, 'in')
                ->create();
        } else {
            $searchCriteria = $this->searchCriteriaBuilderFactory
                ->create()
                ->addFilter('source_code', $sourceCode)
                ->create();
        }
        /** @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository */
        $sourceItemRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceItemRepositoryInterface');
        $sourceItems = $sourceItemRepository->getList($searchCriteria)->getItems();
        $sourceItemsMap = [];
        if ($sourceItems) {
            foreach ($sourceItems as $sourceItem) {
                $sourceItemsMap[$sourceItem->getSku()] = $sourceItem;
            }
        }
        return $sourceItemsMap;
    }
}
