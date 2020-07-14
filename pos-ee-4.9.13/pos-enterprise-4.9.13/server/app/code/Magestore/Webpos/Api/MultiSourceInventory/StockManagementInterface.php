<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\MultiSourceInventory;

/**
 * Interface StockManagementInterface
 * @package Magestore\Webpos\Api\MultiSourceInventory
 */
interface StockManagementInterface
{
    /**
     * @return \Magento\InventoryApi\Api\Data\StockInterface|null
     */
    public function getStock();

    /**
     * @return int|null
     */
    public function getStockId();

    /**
     * @param int $stockId
     * @return string[]
     */
    public function getLinkedSourceCodesByStockId($stockId);

    /**
     * @param int $stockId
     * @return void
     */
    public function addCustomSaleToStock($stockId);

    /**
     * @param $order
     * @return int|null
     */
    public function getStockIdFromOrder($order);

    /**
     * @param \Magento\InventorySalesApi\Api\Data\SalesChannelInterface $salesChannel
     * @return int|null|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStockIdBySalesChannel($salesChannel);

    /**
     * @param string $sku
     * @param string $sourceCode
     * @param int $quantity
     * @param int $status
     * @return mixed
     */
    public function createSourceItem($sku, $sourceCode, $quantity = 0, $status = 1);
}
