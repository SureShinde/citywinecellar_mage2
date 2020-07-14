<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\Data;
use Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface;

/**
 * Class RegisterReaderResponse
 * @package Magestore\WebposStripeTerminal\Model\Data
 */
class RegisterReaderResponse extends \Magento\Framework\DataObject implements RegisterReaderResponseInterface
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getObject()
    {
        return $this->getData(self::OBJECT);
    }

    /**
     * @inheritdoc
     */
    public function setObject($value)
    {
        return $this->setData(self::OBJECT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getDeviceSwVersion()
    {
        return $this->getData(self::DEVICE_SW_VERSION);
    }

    /**
     * @inheritdoc
     */
    public function setDeviceSwVersion($value)
    {
        return $this->setData(self::DEVICE_SW_VERSION, $value);
    }

    /**
     * @inheritdoc
     */
    public function getDeviceType()
    {
        return $this->getData(self::DEVICE_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setDeviceType($value)
    {
        return $this->setData(self::DEVICE_TYPE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getIpAddress()
    {
        return $this->getData(self::IP_ADDRESS);
    }

    /**
     * @inheritdoc
     */
    public function setIpAddress($value)
    {
        return $this->setData(self::IP_ADDRESS, $value);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->getData(self::LOCATION);
    }

    /**
     * @inheritdoc
     */
    public function setLocation($value)
    {
        return $this->setData(self::LOCATION, $value);
    }

    /**
     * @inheritdoc
     */
    public function getSerialNumber()
    {
        return $this->getData(self::SERIAL_NUMBER);
    }

    /**
     * @inheritdoc
     */
    public function setSerialNumber($value)
    {
        return $this->setData(self::SERIAL_NUMBER, $value);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }
}
