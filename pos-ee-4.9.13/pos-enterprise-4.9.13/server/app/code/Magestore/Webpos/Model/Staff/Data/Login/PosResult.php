<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff\Data\Login;

/**
 * Class PosResult
 * @package Magestore\Webpos\Model\Staff\Data\Login
 */
class PosResult extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Staff\Login\PosResultInterface
{
    /**
     * Get pos id
     *
     * @api
     * @return int
     */
    public function getPosId() {
        return $this->getData(self::POS_ID);
    }

    /**
     * Set pos id
     *
     * @api
     * @param int $posId
     * @return $this
     */
    public function setPosId($posId) {
        return $this->setData(self::POS_ID, $posId);
    }

    /**
     * Get pos name
     *
     * @api
     * @return string
     */
    public function getPosName() {
        return $this->getData(self::POS_NAME);
    }

    /**
     * Set location name
     *
     * @api
     * @param string $posName
     * @return $this
     */
    public function setPosName($posName) {
        return $this->setData(self::POS_NAME, $posName);
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
     * Get status
     *
     * @api
     * @return int
     */
    public function getStatus() {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @api
     * @param int $status
     * @return $this
     */
    public function setStatus($status) {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get staff name
     *
     * @api
     * @return string
     */
    public function getStaffName() {
        return $this->getData(self::STAFF_NAME);
    }

    /**
     * Set staff name
     *
     * @api
     * @param int $staffName
     * @return $this
     */
    public function setStaffName($staffName) {
        return $this->setData(self::STAFF_NAME, $staffName);
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
        \Magestore\Webpos\Api\Data\Staff\Login\PosResultExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

}