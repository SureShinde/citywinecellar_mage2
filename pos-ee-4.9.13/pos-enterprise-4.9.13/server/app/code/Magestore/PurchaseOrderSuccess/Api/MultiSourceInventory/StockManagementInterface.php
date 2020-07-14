<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory;

/**
 * Interface StockManagementInterface
 * @package Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory
 */
interface StockManagementInterface
{

    /**
     * @param int $stockId
     * @return string[]
     */
    public function getLinkedSourceCodesByStockId($stockId);

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
