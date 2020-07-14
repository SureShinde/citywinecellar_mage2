<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\MultiSourceInventory;

use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

/**
 * Class StockManagement
 * @package Magestore\DropshipSuccess\Model\MultiSourceInventory
 */
class StockManagement implements \Magestore\DropshipSuccess\Api\MultiSourceInventory\StockManagementInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * StockManagement constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param StockResolverInterface $stockResolver
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        StockResolverInterface $stockResolver
    )
    {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resourceConnection = $resourceConnection;
        $this->stockResolver = $stockResolver;
    }

    /**
     * @return \Magento\InventoryApi\Api\Data\StockInterface|null
     */
    public function getStock()
    {
        $stockId = $this->getStockId();
        if ($stockId) {
            /** @var \Magento\InventoryApi\Api\StockRepositoryInterface $stockRepository */
            $stockRepository = $this->objectManager->create('Magento\InventoryApi\Api\StockRepositoryInterface');
            $stock = $stockRepository->get($stockId);
            return $stock;
        }
        return null;
    }

    /**
     * @return string[]
     */
    public function getLinkedSourceCodesByStockId($stockId)
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter('stock_id', $stockId)
            ->create();
        /** @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLink */
        $getStockSourceLink = $this->objectManager->get('Magento\InventoryApi\Api\GetStockSourceLinksInterface');
        $stockSourcesLink = $getStockSourceLink->execute($searchCriteria);
        $linkedSources = [];
        foreach ($stockSourcesLink->getItems() as $item) {
            $linkedSources[] = $item->getSourceCode();
        }
        return $linkedSources;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStockIdFromOrder($order)
    {
        $websiteCode = $order->getStore()->getWebsite()->getCode();
        $stockId = $this->stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode)->getStockId();
        return $stockId;
    }

    /**
     * @param string $sku
     * @param string $sourceCode
     * @param int $quantity
     * @param int $status
     * @return mixed
     */
    public function createSourceItem($sku, $sourceCode, $quantity = 0, $status = 1)
    {
        /** @var \Magento\InventoryApi\Api\Data\SourceItemInterface $sourceItem */
        $sourceItem = $this->objectManager
            ->create('Magento\InventoryApi\Api\Data\SourceItemInterface');
        $sourceItemData = [
            'sku' => $sku,
            'source_code' => $sourceCode,
            'quantity' => $quantity,
            'status' => $status
        ];
        $this->dataObjectHelper->populateWithArray(
            $sourceItem, $sourceItemData, 'Magento\InventoryApi\Api\Data\SourceItemInterface'
        );
        $sourceItemsForSave = [$sourceItem];
        /** @var \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave */
        $sourceItemSave = $this->objectManager->create('Magento\InventoryApi\Api\SourceItemsSaveInterface');
        $sourceItemSave->execute($sourceItemsForSave);
    }
}
