<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Inventory;

/**
 * Interface StockItemRepository
 * @api
 */
interface StockItemRepositoryInterface
{
    /**
     * Update Stock Item data
     *
     * @param string $itemId
     * @param \Magestore\Webpos\Api\Data\Inventory\StockItemInterface $stockItem
     * @return int
     */
    public function updateStockItem($itemId, \Magestore\Webpos\Api\Data\Inventory\StockItemInterface $stockItem);    
    
     
    /**
     * Update Stock Item data
     *
     * @param \Magestore\Webpos\Api\Data\Inventory\StockItemInterface[] $stockItems
     * @return bool
     */
    public function massUpdateStockItems($stockItems);    
         
     
    /**
     * Load Stock Item data by given stockId and parameters
     *
     * @param int $stockItemId
     * @return \Magestore\Webpos\Api\Data\Inventory\StockItemInterface
     */
    //public function get($stockItemId);

    /**
     * Load Stock Item data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magestore\Webpos\Api\Data\Inventory\StockSearchResultsInterface
     */
    public function getStockItems(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Sync Stock Item data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magestore\Webpos\Api\Data\Inventory\StockSearchResultsInterface
     */
    public function sync(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $product_id
     * @return \Magestore\Webpos\Api\Data\Inventory\AvailableQtyInterface
     */
    public function getAvailableQty($product_id);

    /**
     * @param int $product_id
     * @return \Magestore\Webpos\Api\Data\Inventory\ExternalStockInterface[]
     */
    public function getExternalStock($product_id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Inventory\StockSearchResultsInterface
     */
    public function getStockItemsToRefund(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
