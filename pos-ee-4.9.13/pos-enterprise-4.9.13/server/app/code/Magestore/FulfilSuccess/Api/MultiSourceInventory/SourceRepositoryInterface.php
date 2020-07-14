<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Api\MultiSourceInventory;

/**
 * Interface SourceRepositoryInterface
 * @package Magestore\FulfilSuccess\Api
 */
interface SourceRepositoryInterface
{
    /**
     * Get allow sources to pick from order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    public function getAllowSourcesToPickFromOrder($order);
}