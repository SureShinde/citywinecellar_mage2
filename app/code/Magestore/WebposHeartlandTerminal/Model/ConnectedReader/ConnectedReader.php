<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposHeartlandTerminal\Model\ConnectedReader;

use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Class ConnectedReader
 *
 * @package Magestore\WebposHeartlandTerminal\Model\ConnectedReader
 */
class ConnectedReader extends \Magento\Framework\Model\AbstractModel implements ConnectedReaderInterface
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
    public function getPosId()
    {
        return $this->getData(self::POS_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPosId($value)
    {
        return $this->setData(self::POS_ID, $value);
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
    public function getIpAddress()
    {
        return $this->getData(self::IP_ADDRESS);
    }
    /**
     * @inheritdoc
     */
    public function setPort($value)
    {
        return $this->setData(self::PORT, $value);
    }
    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->getData(self::PORT);
    }

    /**
     * @inheritdoc
     */
    public function setSerialPort($value)
    {
        return $this->setData(self::SERIAL_PORT, $value);
    }
    /**
     * @inheritdoc
     */
    public function getSerialPort()
    {
        return $this->getData(self::SERIAL_PORT);
    }
    /**
     * @inheritdoc
     */
    public function setConnectionMode($value)
    {
        return $this->setData(self::CONNECTION_MODE, $value);
    }
    /**
     * @inheritdoc
     */
    public function getConnectionMode()
    {
        return $this->getData(self::CONNECTION_MODE);
    }
}
