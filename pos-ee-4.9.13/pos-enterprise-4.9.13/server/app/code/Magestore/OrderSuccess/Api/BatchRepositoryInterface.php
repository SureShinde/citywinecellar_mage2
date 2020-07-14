<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Api;

use Magestore\OrderSuccess\Api\Data\BatchInterface;

/**
 * Interface BatchRepositoryInterface
 * @package Magestore\OrderSuccess\Api
 */
interface BatchRepositoryInterface
{
    /**
     * Save Batch.
     *
     * @param \Magestore\OrderSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\OrderSuccess\Api\Data\BatchInterface
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
     * @return \Magestore\OrderSuccess\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($batchId);

    /**
     * Retrieve batchs matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\OrderSuccess\Api\Data\BatchSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve requets in a Batch
     *
     * @param \Magestore\OrderSuccess\Api\Data\BatchInterface $batch
     * @return \Magestore\OrderSuccess\Api\Data\PickRequestSearchResultsInterface
     */
    public function getOrderList(BatchInterface $batch);

    /**
     * Delete batch.
     *
     * @param \Magestore\OrderSuccess\Api\Data\BatchInterface $batch
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

    /**
     * Mass delete batchs
     *
     * @param array $batchIds
     */
    public function massDelete($batchIds);
}