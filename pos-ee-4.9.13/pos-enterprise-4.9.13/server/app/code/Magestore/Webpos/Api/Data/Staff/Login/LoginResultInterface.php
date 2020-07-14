<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Staff\Login;

/**
 * @api
 */
/**
 * Interface LoginResultInterface
 * @package Magestore\Webpos\Api\Data\Staff\Login
 */
interface LoginResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     *
     */
    const STAFF_ID = 'staff_id';
    /**
     *
     */
    const TIME_OUT = 'timeout';
    /**
     *
     */
    const SESSION_ID = 'session_id';
    /**
     *
     */
    const MESSAGE = 'message';
    /**
     *
     */
    const STAFF_NAME = 'staff_name';
    /**
     *
     */
    const LOCATIONS = 'locations';

    /**
     *
     */
    const WEBSITE_ID = 'website_id';

    /**
     *
     */
    const SHARING_ACCOUNT = 'sharing_account';

    const TOKEN = 'token';

    /**
     * Get staff id
     *
     * @api
     * @return int
     */
    public function getStaffId();

    /**
     * Set staff id
     *
     * @api
     * @param int $staffId
     * @return LoginResultInterface
     */
    public function setStaffId($staffId);
    /**
     * Get session time out
     *
     * @api
     * @return int
     */
    public function getTimeout();

    /**
     * Set session time out
     *
     * @api
     * @param int $timeout
     * @return LoginResultInterface
     */
    public function setTimeout($timeout);
    /**
     * Get session id
     *
     * @api
     * @return string
     */
    public function getSessionId();

    /**
     * Set session id
     *
     * @api
     * @param string $sessionId
     * @return LoginResultInterface
     */
    public function setSessionId($sessionId);
    /**
     * Get message
     *
     * @api
     * @return string
     */
    public function getMessage();

    /**
     * Set message
     *
     * @api
     * @param string $message
     * @return LoginResultInterface
     */
    public function setMessage($message);
    /**
     * Get staff name
     *
     * @api
     * @return string
     */
    public function getStaffName();

    /**
     * Set staff name
     *
     * @api
     * @param string $staffName
     * @return LoginResultInterface
     */
    public function setStaffName($staffName);
    /**
     * Get locations
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Staff\Login\LocationResultInterface[]
     */
    public function getLocations();
    /**
     * Set locations
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Staff\Login\LocationResultInterface[] $locations
     * @return LoginResultInterface
     */
    public function setLocations($locations);

    /**
     * Get website id
     *
     * @api
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Set website id
     *
     * @api
     * @param int|null $websiteId
     * @return LoginResultInterface
     */
    public function setWebsiteId($websiteId);


    /**
     * Set SharingAcountInformation
     *
     * @api
     * @param int $sharingAccount
     * @return LoginResultInterface
     */
    public function setSharingAccount($sharingAccount);

    /**
     * Get SharingAcountInformation
     *
     * @api
     * @return int
     */
    public function getSharingAccount();

    /**
     * Set token
     *
     * @api
     * @param string $token
     * @return LoginResultInterface
     */
    public function setToken($token);

    /**
     * Get token
     *
     * @api
     * @return string
     */
    public function getToken();
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Staff\Login\LoginResultExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\Login\LoginResultExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\Login\LoginResultExtensionInterface $extensionAttributes
    );
}
