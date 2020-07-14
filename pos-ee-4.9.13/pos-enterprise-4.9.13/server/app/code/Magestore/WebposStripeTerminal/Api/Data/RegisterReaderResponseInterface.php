<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface RegisterReaderResponseInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface RegisterReaderResponseInterface
{
    const ID = 'id';
    const OBJECT = 'object';
    const DEVICE_SW_VERSION = 'device_sw_version';
    const DEVICE_TYPE = 'device_type';
    const IP_ADDRESS = 'ip_address';
    const LABEL = 'label';
    const LOCATION = 'location';
    const SERIAL_NUMBER = 'serial_number';
    const STATUS = 'status';

    /**
     * @return float|string|null
     */
    public function getId();

    /**
     * @param float|string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setId($value);

    /**
     * @return string|null
     */
    public function getObject();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setObject($value);

    /**
     * @return string|null
     */
    public function getDeviceSwVersion();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setDeviceSwVersion($value);

    /**
     * @return string|null
     */
    public function getDeviceType();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setDeviceType($value);

    /**
     * @return string|null
     */
    public function getIpAddress();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setIpAddress($value);

    /**
     * @return string|null
     */
    public function getLabel();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setLabel($value);

    /**
     * @return string|null
     */
    public function getLocation();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setLocation($value);

    /**
     * @return string|null
     */
    public function getSerialNumber();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setSerialNumber($value);

    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function setStatus($value);
}