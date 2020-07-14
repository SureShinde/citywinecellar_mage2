<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;
/**
 * Class SystemConfig
 * @package Magestore\Webpos\Model\Config\Data
 */
class GuestCustomer extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\GuestCustomerInterface
{
    /**
     * @inheritdoc
     */
    public function getStatus() {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status) {
        return $this->setData(self::STATUS, $status);
    }
    /**
     * @inheritdoc
     */
    public function getFirstName() {
        return $this->getData(self::FIRST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setFirstName($firstName) {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    /**
     * @inheritdoc
     */
    public function getLastName() {
        return $this->getData(self::LAST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setLastName($lastName) {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    /**
     * @inheritdoc
     */
    public function getEmail() {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email) {
        return $this->setData(self::EMAIL, $email);
    }
}