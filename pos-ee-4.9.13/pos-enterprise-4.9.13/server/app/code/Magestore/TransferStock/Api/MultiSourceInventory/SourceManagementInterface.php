<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Api\MultiSourceInventory;

/**
 * Interface SourceManagementInterface
 * @package Magestore\TransferStock\Api\MultiSourceInventory
 */
interface SourceManagementInterface
{
    /**
     * @param string $sku
     * @param array $sourceCodes
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function getSourceItemsMap($sku, $sourceCodes);

    /**
     * @param string $sourceCode
     * @param array $skus
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function getListProductInSource($sourceCode, $skus = []);
}
