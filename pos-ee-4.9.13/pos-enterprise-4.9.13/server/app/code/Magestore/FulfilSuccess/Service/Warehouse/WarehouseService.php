<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Warehouse;

use Magestore\FulfilSuccess\Service\PickRequest\PickRequestService;
use Magento\Framework\ObjectManagerInterface;

class WarehouseService implements WarehouseServiceInterface
{

    /**
     * @var PickRequestService
     */
    protected $pickRequestService;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * @var \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * WarehouseService constructor.
     * @param PickRequestService $pickRequestService
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param ObjectManagerInterface $objectManager
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceItemRepositoryInterface $sourceItemRepository
     */
    public function __construct(
        PickRequestService $pickRequestService,
        \Magento\Framework\Module\Manager $moduleManager,
        ObjectManagerInterface $objectManager,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        \Magestore\FulfilSuccess\Api\MultiSourceInventory\SourceItemRepositoryInterface $sourceItemRepository
    )
    {
        $this->pickRequestService = $pickRequestService;
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
        $this->fulfilManagement = $fulfilManagement;
        $this->sourceItemRepository = $sourceItemRepository;
    }

    /**
     * get list warehouse to pick items
     * $productIds = [$itemId => $productId]
     * return [$itemId => [$warehouseId => $qty]]
     *
     * @param array $productIds
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param bool $showOutStock
     * @return array
     */
    public function getWarehousesToPick($productIds, $order, $showOutStock = false)
    {
        $isMSIEnable = $this->fulfilManagement->isMSIEnable();
        $isInventorySuccessEnable = $this->fulfilManagement->isInventorySuccessEnable();
        if (!$isMSIEnable && !$isInventorySuccessEnable) {
            return [];
        }
        $resourceProducts = [];
        if ($isMSIEnable) {
            $resourceProducts = $this->sourceItemRepository->getSourceItem($productIds, $order);
        } else if ($isInventorySuccessEnable) {
            $warehouseStockRegistry = $this->objectManager->get('Magestore\InventorySuccess\Api\Warehouse\WarehouseStockRegistryInterface');
            $resourceProducts = $warehouseStockRegistry->getStocksWarehouses($productIds);
        }

        $pickingQtys = $this->pickRequestService->getPickingQtyProducts($productIds);
        $warehouses = [];
        foreach ($resourceProducts as $resourceProduct) {
            $resource = $resourceProduct->getWarehouseId();
            $productId = $resourceProduct->getProductId();
            $pickingQty = isset($pickingQtys[$resource][$productId])
                ? $pickingQtys[$resource][$productId]
                : 0;
            $availableQty = max(0, $resourceProduct->getTotalQty() - $pickingQty);
            if (!$showOutStock && !$availableQty) {
                /* do not show out-stock warehouse */
                continue;
            }
            $warehouses[$productId][$resource]['available_qty'] = max(0, $resourceProduct->getTotalQty() - $pickingQty);
            $warehouses[$productId][$resource]['warehouse'] = $resourceProduct->getWarehouse();
            $warehouses[$productId][$resource]['high_priority'] = $resourceProduct->getData('high_priority');
        }

        /* transfer data to item-warehouse */
        $itemWarehouses = [];
        foreach ($productIds as $itemId => $productId) {
            if (!isset($warehouses[$productId])) {
                continue;
            }
            /* sort warehouse by available_qty */
            $itemWarehouses[$itemId] = $this->sortWarehouses($warehouses[$productId]);
        }

        return $itemWarehouses;
    }

    /**
     * @param array $warehouses
     * @return array
     */
    public function sortWarehouses($warehouses) {
        $warehouses = $this->sortWarehousesByQty($warehouses);
        $isMSIEnable = $this->fulfilManagement->isMSIEnable();
        if($isMSIEnable) {
            $warehouses = $this->sortWarehouseByStock($warehouses);
        }
        return $warehouses;
    }

    /**
     * @param array $warehouses
     * @return array
     */
    public function sortWarehouseByStock($warehouses) {
        $highPriorityWarehouses = [];
        foreach ($warehouses as $key => $warehouse) {
            if($warehouse['high_priority']) {
                $highPriorityWarehouses[$key] = $warehouse;
                unset($warehouses[$key]);
            }
        }

        $result = [];
        // merge one by one to fix bug that wrong source code when source is number
        foreach ($highPriorityWarehouses as $k => $wh) {
            $result[$k] = $wh;
        }
        foreach ($warehouses as $k => $wh) {
            $result[$k] = $wh;
        }

        return $result;
    }

    /**
     * Sort warehouse list
     * $warehouses = [$warehouseId => ['available_qty' => $qty, 'warehouse' => $warehouse]]
     *
     * @param array $warehouses
     * @return array
     */
    public function sortWarehousesByQty($warehouses)
    {
        $sortedWarehouses = [];
        foreach ($warehouses as $warehouseId => &$warehouseData) {
            $warehouseData['warehouse_id'] = $warehouseId;
        }

        usort($warehouses, [$this, "sortWarehousesByQtyDESC"]);

        foreach ($warehouses as $warehouse) {
            $sortedWarehouses[$warehouse['warehouse_id']] = $warehouse;
        }

        return $sortedWarehouses;
    }

    /**
     * Compare lack_qty of warehouses
     *
     * @param array $warehouseA
     * @param array $warehouseB
     * @return int
     */
    public function sortWarehousesByQtyDESC($warehouseA, $warehouseB)
    {
        if ($warehouseA['available_qty'] == $warehouseB['available_qty'])
            return 0;
        if ($warehouseA['available_qty'] > $warehouseB['available_qty'])
            return -1;
        return 1;
    }

}