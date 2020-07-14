<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff;
class Session extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Staff\SessionInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Staff\Session');
    }

    /**
     * Get id
     *
     * @api
     * @return string|null
     */
    public function getId() {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     *
     * @api
     * @param int $id
     * @return $this
     */
    public function setId($id) {
        return $this->setData(self::ID, $id);
    }
    /**
     * Get staff id
     *
     * @api
     * @return int
     */
    public function getStaffId() {
        return $this->getData(self::STAFF_ID);
    }

    /**
     * Set staff id
     *
     * @api
     * @param int $staffId
     * @return $this
     */
    public function setStaffId($staffId) {
        return $this->setData(self::STAFF_ID, $staffId);
    }
    /**
     * Get logged date
     *
     * @api
     * @return string
     */
    public function getLoggedDate() {
        return $this->getData(self::LOGGED_DATE);
    }

    /**
     * Set logged date
     *
     * @api
     * @param string $loggedDate
     * @return $this
     */
    public function setLoggedDate($loggedDate) {
        return $this->setData(self::LOGGED_DATE, $loggedDate);
    }
    /**
     * Get session
     *
     * @api
     * @return string
     */
    public function getSessionId() {
        return $this->getData(self::SESSION_ID);
    }

    /**
     * Set session
     *
     * @api
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId($sessionId) {
        return $this->setData(self::SESSION_ID, $sessionId);
    }


    /**
     * get location id
     *
     * @api
     * @return int
     */
    public function getLocationId(){
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set location id
     *
     * @api
     * @param string $locationId
     * @return $this
     */
    public function setLocationId($locationId){
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * get session
     *
     * @api
     * @return int
     */
    public function getPosId(){
        return $this->getData(self::POS_ID);
    }

    /**
     * Set pos Id
     *
     * @api
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId){
        return $this->setData(self::POS_ID, $posId);
    }

    /**
     * @inheritdoc
     */
    public function getHasException(){
        return $this->getData(self::HAS_EXCEPTION);
    }

    /**
     * @inheritdoc
     */
    public function setHasException($hasException){
        return $this->setData(self::HAS_EXCEPTION, $hasException);
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Staff\SessionExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}