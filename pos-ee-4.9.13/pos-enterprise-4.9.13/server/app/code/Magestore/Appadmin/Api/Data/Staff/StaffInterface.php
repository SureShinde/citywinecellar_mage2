<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Api\Data\Staff;

/**
 * Interface StaffInterface
 * @package Magestore\Appadmin\Api\Data\Staff
 */
interface StaffInterface
{
    /**
     *
     */
    const STAFF_ID = "staff_id";
    /**
     *
     */
    const USER_NAME = 'username';
    /**
     *
     */
    const PASSWORD = 'password';
    /**
     *
     */
    const NAME = 'name';
    /**
     *
     */
    const EMAIL = 'email';
    /**
     *
     */
    const CUSTOMER_GROUPS = 'customer_groups';
    /**
     *
     */
    const LOCATION_IDS = 'location_ids';
    /**
     *
     */
    const ROLE_ID = 'role_id';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const STATUS_ENABLED = 1;
    /**
     *
     */
    const MIN_PASSWORD_LENGTH = 7;
    /**
     *
     */
    const POS_IDS = 'pos_ids';

    /**
     *
     */
    const PIN = 'pin';
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
     * @return $this
     */
    public function setStaffId($staffId);
    /**
     * Get user name
     *
     * @api
     * @return string|null
     */
    public function getUsername();

    /**
     * Set user name
     *
     * @api
     * @param string $username
     * @return $this
     */
    public function setUsername($username);
    /**
     * Get password param
     *
     * @api
     * @return string|null
     */
    public function getPassword();

    /**
     * Set password param
     *
     * @api
     * @param string $password
     * @return $this
     */
    public function setPassword($password);
    /**
     * Get display name
     *
     * @api
     * @return string|null
     */
    public function getName();

    /**
     * Set display name
     *
     * @api
     * @param string $name
     * @return $this
     */
    public function setName($name);
    /**
     * Get email
     *
     * @api
     * @return string|null
     */
    public function getEmail();

    /**
     * Set display name
     *
     * @api
     * @param string $email
     * @return $this
     */
    public function setEmail($email);
    /**
     * Get customer group
     *
     * @api
     * @return string|null
     */
    public function getCustomerGroups();

    /**
     * Set customer group
     *
     * @api
     * @param string $customerGroups
     * @return $this
     */
    public function setCustomerGroups($customerGroups);
    /**
     * Get location id
     *
     * @api
     * @return string|null
     */
    public function getLocationIds();

    /**
     * Set location id
     *
     * @api
     * @param string $locationIds
     * @return $this
     */
    public function setLocationIds($locationIds);
    /**
     * Get role id
     *
     * @api
     * @return string|null
     */
    public function getRoleId();

    /**
     * Set role id
     *
     * @api
     * @param string $roleId
     * @return $this
     */
    public function setRoleId($roleId);
    /**
     * Get status
     *
     * @api
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @api
     * @param string $status
     * @return $this
     */
    public function setStatus($status);
    /**
     * Get pos id
     *
     * @api
     * @return string|null
     */
    public function getPosIds();

    /**
     * Set pos id
     *
     * @api
     * @param string $posIds
     * @return $this
     */
    public function setPosIds($posIds);
}
