<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Api\Staff;

interface RoleRepositoryInterface
{
    /**
     * Save Role.
     *
     * @param \Magestore\Appadmin\Api\Data\Staff\RoleInterface $role
     * @return \Magestore\Appadmin\Api\Data\Staff\RoleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Appadmin\Api\Data\Staff\RoleInterface $role);

    /**
     * Retrieve role.
     *
     * @param int $roleId
     * @return \Magestore\Appadmin\Api\Data\Staff\RoleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($roleId);

    /**
     * Retrieve role matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Appadmin\Api\Data\Staff\RoleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Role.
     *
     * @param \Magestore\Appadmin\Api\Data\Staff\RoleInterface $role
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Appadmin\Api\Data\Staff\RoleInterface $role);

    /**
     * Delete role by ID.
     *
     * @param int $roleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($roleId);

    /**
     * Get role collection.
     * @return \Magestore\Appadmin\Api\Data\Staff\RoleInterface[]
     */
    public function getAllRole();
}
