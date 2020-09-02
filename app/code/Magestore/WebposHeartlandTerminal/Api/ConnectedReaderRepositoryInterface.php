<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposHeartlandTerminal\Api;

use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Interface ConnectedReaderRepositoryInterface
 *
 * @package Magestore\WebposHeartlandTerminal\Api
 */
interface ConnectedReaderRepositoryInterface
{
    /**
     * Save connectedReader.
     *
     * @param ConnectedReaderInterface $connectedReader
     * @return ConnectedReaderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ConnectedReaderInterface $connectedReader);

    /**
     * Retrieve pos.
     *
     * @param int $connectedReaderId
     * @return ConnectedReaderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($connectedReaderId);

    /**
     * Delete connectedReader.
     *
     * @param ConnectedReaderInterface $connectedReader
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ConnectedReaderInterface $connectedReader);

    /**
     * Delete connectedReader by ID.
     *
     * @param int $connectedReaderId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function deleteById($connectedReaderId);

    /**
     * Retrieve connectedReader collection.
     *
     * @param int $posId
     * @return ConnectedReaderInterface
     */
    public function getConnectedReaderByPosId($posId);

    /**
     * Retrieve pos matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
