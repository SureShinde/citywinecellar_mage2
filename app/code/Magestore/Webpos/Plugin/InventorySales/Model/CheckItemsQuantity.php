<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\InventorySales\Model;


class CheckItemsQuantity
{
    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * StockIdResolver constructor.
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     */
    public function __construct(
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
    )
    {
        $this->stockManagement = $stockManagement;
    }

    /**
     * @param \Magento\InventorySales\Model\CheckItemsQuantity $subject
     * @param callable $proceed
     * @param array $items
     * @param int $stockId
     * @return mixed
     */
    public function aroundExecute(
        $subject,
        callable $proceed,
        array $items,
        int $stockId
    )
    {
        if ($this->stockManagement->getStockId()) {
            return true;
        }
        return $proceed($items, $stockId);
    }
}