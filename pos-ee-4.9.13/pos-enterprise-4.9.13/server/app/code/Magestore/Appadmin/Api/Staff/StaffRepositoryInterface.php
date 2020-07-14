<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Api\Staff;

interface StaffRepositoryInterface
{
    /**
     * Save staff.
     *
     * @param \Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff
     * @return \Magestore\Appadmin\Api\Data\Staff\StaffInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff);

    /**
     * Retrieve staff.
     *
     * @param int $staffId
     * @return \Magestore\Appadmin\Api\Data\Staff\StaffInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($staffId);

    /**
     * Retrieve staff.
     *
     * @param int $roleId
     * @return \Magestore\Appadmin\Api\Data\Staff\StaffInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByRoleId($roleId);

    /**
     * Retrieve staff matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Appadmin\Api\Data\Staff\StaffSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete staff.
     *
     * @param \Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff);

    /**
     * Delete staff by ID.
     *
     * @param int $staffId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($staffId);

    /**
     * Get location collection.
     * @return \Magestore\Appadmin\Api\Data\Staff\StaffInterface[]
     */
    public function getAllStaff();

}
