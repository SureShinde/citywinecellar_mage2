<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

/**
 * Interface PackRequestItemRepositoryInterface
 * @package Magestore\FulfilSuccess\Api
 * @api
 */
interface PackRequestItemRepositoryInterface
{
    /**
     * Lists pack request items that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria The search criteria.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemSearchResultInterface Pack request item search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);

    /**
     * 
     * @param array $productIds
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface[]
     */
    public function getPackingList($productIds=[]);    
    
    /**
     * Loads a specified pack request item.
     *
     * @param int $packRequestItemId The pack request item ID.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface Pack Request Item interface.
     */
    public function get($packRequestItemId);

    /**
     * Deletes a specified pack request item.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface $packRequestItem The pack request item.
     * @return bool
     */
    public function delete(\Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface $packRequestItem);

    /**
     * Performs persist operations for a specified pack request item.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface $packRequestItem The Pack request item.
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface Pack Request Item interface.
     */
    public function save(\Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface $packRequestItem);
}