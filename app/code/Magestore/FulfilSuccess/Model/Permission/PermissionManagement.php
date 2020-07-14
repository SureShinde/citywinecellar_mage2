<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Model\Permission;
use Magestore\FulfilSuccess\Api\PermissionManagementInterface;

/**
 * Class PermissionManagement
 * @package Magestore\FulfilSuccess\Model\Permission
 */
class PermissionManagement implements PermissionManagementInterface
{
    /**
     * @var \Magento\Framework\Authorization\PolicyInterface
     */
    protected $_policyInterface;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    protected function _construct()
    {
        /* do nothing */
    }

    /**
     * PermissionManagement constructor.
     * @param \Magento\Framework\Authorization\PolicyInterface $policyInterface
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magento\Framework\Authorization\PolicyInterface $policyInterface,
        \Magento\Backend\Model\Auth\Session $authSession
    )
    {
        $this->_policyInterface = $policyInterface;
        $this->_authSession = $authSession;
    }

    /**
     * @param $resourceId
     * @param null $user
     * @return bool
     */
    public function checkPermission($resourceId, $user = null)
    {
        if (!$user) {
            $user = $this->_authSession->getUser();
        }
        $permission = $this->_policyInterface->isAllowed($user->getRole()->getId(), $resourceId);

        return $permission;
    }
}