<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\MultiSourceInventory;

use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;

/**
 * Class StockManagement
 *
 * @package Magestore\Webpos\Model\MultiSourceInventory
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockManagement implements \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
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
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\SourceManagementInterface
     */
    protected $sourceManagement;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;

    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

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
     * StockManagement constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param \Magestore\Webpos\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        \Magestore\Webpos\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        $this->webposManagement = $webposManagement;
        $this->sourceManagement = $sourceManagement;
        $this->productRepository = $productRepository;
        $this->sessionRepository = $sessionRepository;
        $this->locationRepository = $locationRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get stock
     *
     * @return \Magento\InventoryApi\Api\Data\StockInterface|null
     */
    public function getStock()
    {
        $stockId = $this->getStockId();
        if ($stockId) {
            /** @var \Magento\InventoryApi\Api\StockRepositoryInterface $stockRepository */
            $stockRepository = $this->objectManager->create(
                \Magento\InventoryApi\Api\StockRepositoryInterface::class
            );
            $stock = $stockRepository->get($stockId);
            return $stock;
        }
        return null;
    }

    /**
     * Get stock id
     *
     * @return int|null
     */
    public function getStockId()
    {
        $stockId = $this->coreRegistry->registry('webpos_current_stock_id');
        if ($stockId) {
            return $stockId;
        }
        if ($this->webposManagement->isMSIEnable()) {
            try {
                $sessionId = $this->request->getParam(
                    \Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY
                );
                $sessionLogin = $this->sessionRepository->getBySessionId($sessionId);
                $locationId = $sessionLogin->getLocationId();
            } catch (\Exception $e) {
                $locationId = $this->request->getParam(
                    \Magestore\Webpos\Model\Checkout\PosOrder::PARAM_ORDER_LOCATION_ID
                );
            }

            if (!$locationId) {
                return null;
            }
            if ($this->webposManagement->isWebposStandard()) {
                /** @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider */
                $defaultStockProvider = $this->objectManager->create(
                    \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface::class
                );
                $stockId = $defaultStockProvider->getId();
                $this->coreRegistry->register('webpos_current_stock_id', $stockId);
                return $defaultStockProvider->getId();
            } else {
                $location = $this->locationRepository->getById($locationId);
                $stockId = $location->getStockId();
                $this->coreRegistry->register('webpos_current_stock_id', $stockId);
                return $stockId;
            }
        }
        return null;
    }

    /**
     * Get linked source code
     *
     * @param int $stockId
     * @return array|string[]
     */
    public function getLinkedSourceCodesByStockId($stockId)
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter('stock_id', $stockId)
            ->create();
        /** @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLink */
        $getStockSourceLink = $this->objectManager->get(
            \Magento\InventoryApi\Api\GetStockSourceLinksInterface::class
        );
        $stockSourcesLink = $getStockSourceLink->execute($searchCriteria);
        $linkedSources = [];
        foreach ($stockSourcesLink->getItems() as $item) {
            $linkedSources[] = $item->getSourceCode();
        }
        return $linkedSources;
    }

    /**
     * Add custom sale
     *
     * @param int $stockId
     * @return \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    public function addCustomSaleToStock($stockId)
    {
        if (!$this->webposManagement->isMSIEnable()) {
            return $this;
        }

        try {
            $product = $this->productRepository->get(\Magestore\Webpos\Helper\Product\CustomSale::SKU);
            if ($product->getId()) {
                $sourceCodes = $this->getLinkedSourceCodesByStockId($stockId);
                if (empty($sourceCodes)) {
                    return $this;
                }
                $sourceItemsMap = $this->sourceManagement->getSourceItemsMap(
                    \Magestore\Webpos\Helper\Product\CustomSale::SKU,
                    $sourceCodes
                );
                foreach ($sourceCodes as $sourceCode) {
                    if (isset($sourceItemsMap[$sourceCode])) {
                        return $this;
                    }
                    $this->createSourceItem(
                        \Magestore\Webpos\Helper\Product\CustomSale::SKU,
                        $sourceCode,
                        0,
                        1
                    );
                }
            }
        } catch (\Exception $e) {
            return $this;
        }

        return $this;
    }

    /**
     * Get stock id
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int|null
     */
    public function getStockIdFromOrder($order)
    {
        $locationId = $order->getPosLocationId();
        if ($locationId) {
            $location = $this->locationRepository->getById($locationId);
            if ($location->getLocationId()) {
                return $location->getStockId();
            }
            return null;
        }
        return null;
    }

    /**
     * Get stock id
     *
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
     * Get source item
     *
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
            ->create(\Magento\InventoryApi\Api\Data\SourceItemInterface::class);
        $sourceItemData = [
            'sku' => $sku,
            'source_code' => $sourceCode,
            'quantity' => $quantity,
            'status' => $status
        ];
        $this->dataObjectHelper->populateWithArray(
            $sourceItem,
            $sourceItemData,
            \Magento\InventoryApi\Api\Data\SourceItemInterface::class
        );
        $sourceItemsForSave = [$sourceItem];
        /** @var \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave */
        $sourceItemSave = $this->objectManager->create(\Magento\InventoryApi\Api\SourceItemsSaveInterface::class);
        $sourceItemSave->execute($sourceItemsForSave);
    }
}
