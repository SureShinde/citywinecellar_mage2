<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Staff;

interface SessionRepositoryInterface
{
    /**
     * Save Session.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\SessionInterface $Session
     * @return \Magestore\Webpos\Api\Data\Staff\SessionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Staff\SessionInterface $Session);

    /**
     * Retrieve Session.
     *
     * @param int $id
     * @return \Magestore\Webpos\Api\Data\Staff\SessionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve Session.
     *
     * @param int $sessionId
     * @return \Magestore\Webpos\Api\Data\Staff\SessionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySessionId($sessionId);

    /**
     * Retrieve Session matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Staff\SessionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve Session matching the specified criteria.
     *
     * @param int $staffId
     * @return \Magestore\Webpos\Model\ResourceModel\Staff\Session\Collection
     */
    public function getListByStaffId($staffId);

    /**
     * Delete Session.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\SessionInterface $Session
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Webpos\Api\Data\Staff\SessionInterface $Session);

    /**
     * Delete Session by ID.
     *
     * @param int $sessionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($sessionId);


    /**
     * @param int $staffId
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllCurrentPermission($staffId);

    /**
     * @param int $posId
     * @return mixed
     */
    public function signOutPos($posId);
}
