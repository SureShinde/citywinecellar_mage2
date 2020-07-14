<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Staff;

/**
 * Interface SessionInterface
 * @package Magestore\Webpos\Api\Data\Staff
 */
interface SessionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     *
     */
    const ID = "id";
    /**
     *
     */
    const STAFF_ID = 'staff_id';
    /**
     *
     */
    const LOGGED_DATE = 'logged_date';
    /**
     *
     */
    const SESSION_ID = 'session_id';
    const LOCATION_ID = 'location_id';
    const POS_ID = 'pos_id';
    const HAS_EXCEPTION = 'has_exception';
    /**
     * Get id
     *
     * @api
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     *
     * @api
     * @param int $id
     * @return SessionInterface
     */
    public function setId($id);
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
     * @return SessionInterface
     */
    public function setStaffId($staffId);
    /**
     * Get logged date
     *
     * @api
     * @return string
     */
    public function getLoggedDate();

    /**
     * Set logged date
     *
     * @api
     * @param string $loggedDate
     * @return SessionInterface
     */
    public function setLoggedDate($loggedDate);
    /**
     * Get session
     *
     * @api
     * @return string
     */
    public function getSessionId();

    /**
     * Set session
     *
     * @api
     * @param string $sessionId
     * @return SessionInterface
     */
    public function setSessionId($sessionId);

    /**
     * get location id
     *
     * @api
     * @return int
     */
    public function getLocationId();

    /**
     * Set location id
     *
     * @api
     * @param string $locationId
     * @return SessionInterface
     */
    public function setLocationId($locationId);

    /**
     * get session
     *
     * @api
     * @return int
     */
    public function getPosId();

    /**
     * Set pos Id
     *
     * @api
     * @param string $posId
     * @return SessionInterface
     */
    public function setPosId($posId);

    /**
     * get has exception
     *
     * @api
     * @return int
     */
    public function getHasException();

    /**
     * Set has exception
     *
     * @api
     * @param int $hasException
     * @return SessionInterface
     */
    public function setHasException($hasException);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Staff\SessionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\SessionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\SessionExtensionInterface $extensionAttributes
    );
}
