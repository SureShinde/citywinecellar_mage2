<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Model\MultiSourceInventory;

use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;

/**
 * Class StockManagement
 * @package Magestore\AdjustStock\Model\MultiSourceInventory
 */
class StockManagement implements \Magestore\AdjustStock\Api\MultiSourceInventory\StockManagementInterface
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
     * @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface
     */
    protected $getStockSourceLink;
    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    protected $sourceItemSave;

    /**
     * StockManagement constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLink
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLink,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
    )
    {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resourceConnection = $resourceConnection;
        $this->getStockSourceLink = $getStockSourceLink;
        $this->sourceItemSave = $sourceItemSave;
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
        $stockSourcesLink = $this->getStockSourceLink->execute($searchCriteria);
        $linkedSources = [];
        foreach ($stockSourcesLink->getItems() as $item) {
            $linkedSources[] = $item->getSourceCode();
        }
        return $linkedSources;
    }

    /**
     * @param \Magento\InventorySalesApi\Api\Data\SalesChannelInterface $salesChannel
     * @return int|null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStockIdBySalesChannel($salesChannel)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_stock_sales_channel');

        $select = $connection->select()
            ->from($tableName, 'stock_id')
            ->where(SalesChannelInterface::TYPE . ' = ?', $salesChannel->getType())
            ->where(SalesChannelInterface::CODE . ' = ?', $salesChannel->getCode());

        $stockId = $connection->fetchOne($select);
        $stockId = false === $stockId ? null : (int)$stockId;
        if (null === $stockId) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('No linked stock found'));
        }
        return $stockId;
    }

    /**
     * @param string $sku
     * @param string $sourceCode
     * @param int $quantity
     * @param int $status
     * @return mixed|void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
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
        $this->sourceItemSave->execute($sourceItemsForSave);
    }
}
