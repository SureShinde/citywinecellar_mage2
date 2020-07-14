<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Pos;

interface PosRepositoryInterface
{
    /**
     * Save pos.
     *
     * @param \Magestore\Webpos\Api\Data\Pos\PosInterface $pos
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Pos\PosInterface $pos);

    /**
     * Retrieve pos.
     *
     * @param int $posId
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($posId);

    /**
     * Delete pos.
     *
     * @param \Magestore\Webpos\Api\Data\Pos\PosInterface $pos
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Webpos\Api\Data\Pos\PosInterface $pos);

    /**
     * Delete pos by ID.
     *
     * @param int $posId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($posId);

    /**
     * Retrieve pos.
     *
     * @param \Magestore\Webpos\Api\Data\Pos\EnterPosInterface $pos
     * @return \Magestore\Webpos\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function assignPos($pos);

    /**
     * Retrieve pos collection.
     *
     * @param int $locationId
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface[]
     */
    public function getPosByLocationId($locationId);

    /**
     * Get location collection.
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface[]
     */
    public function getAllPos();

    /**
     * Retrieve pos matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve pos
     *
     * @param int $posId
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface
     */
    public function freePosById($posId);
}
