<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Api;

use Magestore\FulfilSuccess\Api\Data\BatchInterface;

interface BatchRepositoryInterface
{
    /**
     * Save Batch.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\FulfilSuccess\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BatchInterface $batch);

    /**
     * create a new batch
     * 
     * @return BatchInterface
     */
    public function newBatch();    
    
    /**
     * Retrieve batch.
     *
     * @param int $batchId
     * @return \Magestore\FulfilSuccess\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($batchId);

    /**
     * Retrieve batchs matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\FulfilSuccess\Api\Data\BatchSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    
    /**
     * Retrieve requets in a Batch
     * 
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestSearchResultsInterface
     */
    public function getPickRequestList(BatchInterface $batch);

    /**
     * Delete batch.
     *
     * @param \Magestore\FulfilSuccess\Api\Data\BatchInterface $batch
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BatchInterface $batch);

    /**
     * Delete batch by ID.
     *
     * @param int $batchId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($batchId);    
}