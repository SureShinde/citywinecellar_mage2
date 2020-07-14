<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Staff;

/**
 * Class StaffManagement
 * @package Magestore\Appadmin\Model\Staff
 */
class RuleRepository implements \Magestore\Appadmin\Api\Staff\RuleRepositoryInterface
{
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;
    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Appadmin\Model\ResourceModel\Staff\AuthorizationRule\CollectionFactory $collectionFactory
    ) {
        $this->staffRepository = $staffRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function isAllowPermission($aclResource, $staffId){
        $permissions = $this->getAllPermissionByStaffId($staffId);
        if (in_array('Magestore_Appadmin::all', $permissions) || in_array($aclResource, $permissions)) {
            return true;
        }
        return false;
    }

    /**
     * @param int $staffId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllPermissionByStaffId($staffId) {
        $staffModel = $this->staffRepository->getById($staffId);
        $resourceAccess = array();
        if ($staffModel->getId()) {
            $roleId = $staffModel->getRoleId();
            $resourceAccess = $this->getAllPermissionByRoleId($roleId);
        }
        return $resourceAccess;
    }

    /**
     * @param int $roleId
     * @return array
     */
    public function getAllPermissionByRoleId($roleId) {
        $resourceAccess = array();
        $authorizationCollection = $this->collectionFactory->create()->addFieldToFilter('role_id', $roleId);
        foreach ($authorizationCollection as $resource) {
            $resourceAccess[] = $resource->getResourceId();
        }
        return $resourceAccess;
    }
}