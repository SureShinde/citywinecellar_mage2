<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Api\MultiSourceInventory;

/**
 * Interface SourceItemRegistryInterface
 * @package Magestore\FulfilSuccess\Api
 */
interface SourceItemRepositoryInterface
{
    /**
     * @param int[] $productIds
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function getSourceItem($productIds, $order);
}