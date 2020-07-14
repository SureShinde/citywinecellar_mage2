<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\MultiSourceInventory;

/**
 * Interface StockManagementInterface
 * @package Magestore\DropshipSuccess\Api\MultiSourceInventory
 */
interface StockManagementInterface
{
    /**
     * @return \Magento\InventoryApi\Api\Data\StockInterface|null
     */
    public function getStock();

    /**
     * @param int $stockId
     * @return string[]
     */
    public function getLinkedSourceCodesByStockId($stockId);

    /**
     * @param $order
     * @return int|null
     */
    public function getStockIdFromOrder($order);

    /**
     * @param string $sku
     * @param string $sourceCode
     * @param int $quantity
     * @param int $status
     * @return mixed
     */
    public function createSourceItem($sku, $sourceCode, $quantity = 0, $status = 1);
}
