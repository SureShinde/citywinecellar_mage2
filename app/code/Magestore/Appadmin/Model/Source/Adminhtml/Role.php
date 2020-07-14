<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Source\Adminhtml;
/**
 * Class Role
 * @package Magestore\Appadmin\Model\Source\Adminhtml
 */
class Role implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface
     */
    public $roleRepository;

    /**
     * Role constructor.
     * @param \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface $roleRepository
     */
    public function __construct(
        \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface $roleRepository
    ){
        $this->roleRepository = $roleRepository;
    }


    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allRole = $this->roleRepository->getAllRole();
        $allRoleArray = [];
        $allRoleArray[] = ['label' => ' ', "value" => null];
        foreach ($allRole as $role) {
            $allRoleArray[] = ['label' => $role->getName(), 'value' => $role->getRoleId()];
        }
        return $allRoleArray;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $allRole = $this->roleRepository->getAllRole();
        $allRoleArray = [];
        foreach ($allRole as $role) {
            $allRoleArray[$role->getRoleId()] = $role->getName();
        }
        return $allRoleArray;
    }

}