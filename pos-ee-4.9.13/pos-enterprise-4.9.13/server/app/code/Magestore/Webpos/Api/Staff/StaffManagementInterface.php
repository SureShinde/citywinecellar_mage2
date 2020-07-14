<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Staff;

interface StaffManagementInterface
{

    /**
     * @param \Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff
     * @return \Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface
     * @throws \Magento\Framework\Exception\AuthorizationException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function login($staff);


    /**
     *  @return \Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface
     */
    public function continueLogin();

    /**
     * @return \Magestore\Webpos\Api\Data\Staff\Logout\LogoutResultInterface
     */

    public function logout();

    /**
     * @param string $resource
     * @param int $staffId
     * @return bool
     */
    public function isAllowResource($resource, $staffId);
    /**
     * @param string $phpSession
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function authorizeSession($phpSession);

}
