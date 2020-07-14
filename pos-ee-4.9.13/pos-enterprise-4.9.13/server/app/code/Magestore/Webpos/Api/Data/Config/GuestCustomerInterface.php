<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * @api
 */
/**
 * Interface ConfigInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface GuestCustomerInterface
{
    const STATUS = 'status';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const EMAIL = 'email';

    /**
     * Get status
     *
     * @api
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @api
     * @param int $status
     * @return GuestCustomerInterface
     */
    public function setStatus($status);

    /**
     * Get first name
     *
     * @api
     * @return string
     */
    public function getFirstName();

    /**
     * Set first name
     *
     * @api
     * @param string $firstName
     * @return GuestCustomerInterface
     */
    public function setFirstName($firstName);

    /**
     * Get last name
     *
     * @api
     * @return string
     */
    public function getLastName();

    /**
     * Set last name
     *
     * @api
     * @param string $lastName
     * @return GuestCustomerInterface
     */
    public function setLastName($lastName);

    /**
     * Get email
     *
     * @api
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @api
     * @param string $email
     * @return GuestCustomerInterface
     */
    public function setEmail($email);
}