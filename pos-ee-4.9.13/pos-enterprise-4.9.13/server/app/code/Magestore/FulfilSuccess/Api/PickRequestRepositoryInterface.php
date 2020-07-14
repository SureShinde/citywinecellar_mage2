<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

interface PickRequestRepositoryInterface
{
    /**
     * Save PickRequest.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\PickRequestInterface $pickRequest);

    /**
     * Retrieve PickRequest.
     *
     * @param int $pickRequestId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($pickRequestId);

    /**
     * Retrieve PickRequests matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Retrieve items list from PickRequest
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemList(Data\PickRequestInterface $pickRequest);
    
    /**
     * Retrieve PickRequestItem in PickRequest
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @param int $itemId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface
     */
    public function getPickRequestItem(Data\PickRequestInterface $pickRequest, $itemId);

    /**
     * Mass Update items in PickRequest
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @param array $itemsData
     */
    public function massUpdateItems(Data\PickRequestInterface $pickRequest, $itemsData);
    
    /**
     * Mass update batch Id of Pick Requests
     * 
     * @param array $pickRequestIds
     * @param int $batchId
     */
    public function massUpdateBatch($pickRequestIds, $batchId);    
    
    /**
     * Delete PickRequest.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $pickRequest
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\PickRequestInterface $pickRequest);

    /**
     * Delete PickRequest by ID.
     *
     * @param int $pickRequestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pickRequestId);

    /**
     * Retrieve PickRequest.
     *
     * @param string $incrementId
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface
     */
    public function getByOrderIncrementId($incrementId);

    /**
     * @param \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest
     * @param array $data
     */
    public function massUpdatePickRequestItems($pickRequest, $data);
}