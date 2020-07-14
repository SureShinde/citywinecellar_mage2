<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory;

/**
 * Interface SourcekManagementInterface
 * @package Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory
 */
interface SourceManagementInterface
{
    /**
     * @param string $sku
     * @param array $sourceCodes
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function getSourceItemsMap($sku, $sourceCodes);
}
