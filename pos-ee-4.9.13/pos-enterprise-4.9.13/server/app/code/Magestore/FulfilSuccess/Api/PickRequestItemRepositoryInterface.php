<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

interface PickRequestItemRepositoryInterface
{
    /**
     * Save PickRequestItem.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface $pickRequestItem
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\PickRequestItemInterface $pickRequestItem);

    /**
     * Retrieve BatchPickRequest.
     *
     * @param int $pickRequestItemId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pickRequestItemId);

    /**
     * Get list by Product Id
     * 
     * @param int $productId
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getListByProductId($productId); 
    
    /**
     * Get list by Product Ids
     * 
     * @param array $productIds
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getListByProductIds($productIds);
    
    /**
     * Get picking item list
     * 
     * @param array $productIds
     * @retrurn \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getPickingList($productIds=[]);
    
    /**
     * Delete PickRequestItem.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface $pickRequestItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\PickRequestItemInterface $pickRequestItem);

    /**
     * Delete PickRequestItem by ID.
     *
     * @param int $pickRequestItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pickRequestItemId);

    /**
     * @param string $pickRequestId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     */
    public function getListByRequestId($pickRequestId);

    /**
     * @param int $pickRequestId
     * @param int $itemId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByRequestIdAndItemId($pickRequestId, $itemId);
}