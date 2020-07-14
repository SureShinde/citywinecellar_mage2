<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Model\MultiSourceInventory;

/**
 * Class SourceItemRepository
 * @package Magestore\FulfilSuccess\Model\MultiSourceInventory
 */
class SourceItemRepository implements \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceItemRepositoryInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * @var \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $msiSourceRepository;

    /**
     * SourceItemRepository constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceRepositoryInterface $sourceRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $msiSourceRepository
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceRepositoryInterface $sourceRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $msiSourceRepository
    )
    {
        $this->objectManager = $objectManager;
        $this->fulfilManagement = $fulfilManagement;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->msiSourceRepository = $msiSourceRepository;
    }

    /**
     * @param int[] $productIds
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function getSourceItem($productIds, $order)
    {
        try {
            $resourceProducts = [];

            // get sources which are assigned to stock of sale channel where the order was created
            $sourceInStock = $this->sourceRepository->getAllowSourcesToPickFromOrder($order);
            // get sources which are assigned to stock of sale channel where the order was created

            // get all enabled sources
            $sourceSearchCriteria = $this->searchCriteriaBuilder
                ->addFilter('enabled', \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES, 'eq')
                ->create();
            $sourceCodesCollection = $this->msiSourceRepository->getList($sourceSearchCriteria);
            $sourceCodes = [];
            foreach ($sourceCodesCollection->getItems() as $item) {
                $sourceCodes[$item->getSourceCode()] = $item;
            }

            /** @var \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds */
            $getSkusByProductIds = $this->objectManager
                ->get('Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface');
            /** @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository */
            $sourceItemRepository = $this->objectManager->get('Magento\InventoryApi\Api\SourceItemRepositoryInterface');
            $skus = $getSkusByProductIds->execute($productIds);
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('source_code', array_keys($sourceCodes), 'in')
                ->addFilter('sku', array_values($skus), 'in')
                ->create();
            $sourceItems = $sourceItemRepository->getList($searchCriteria)->getItems();
            foreach ($sourceItems as $sourceItem) {
                $productId = array_search($sourceItem->getSku(), $skus);
                if ($productId) {
                    $source = $sourceCodes[$sourceItem->getSourceCode()];
                    $sourceItem->setData('product_id', $productId);
                    $sourceItem->setData('available_qty', $sourceItem->getQuantity());
                    $sourceItem->setData('total_qty', $sourceItem->getQuantity());
                    $sourceItem->setData('warehouse_id', $sourceItem->getSourceCode());
                    $sourceItem->setData('warehouse', $source->getName() . " (" . $sourceItem->getSourceCode() . ")");
                    $sourceItem->setData('high_priority', in_array($sourceItem->getSourceCode(), array_keys($sourceInStock)) ? 1 : 0);
                    $resourceProducts[] = $sourceItem;
                }
            }
            return $resourceProducts;
        } catch (\Exception $e) {
            return [];
        }
    }
}
