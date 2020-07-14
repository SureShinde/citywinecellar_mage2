<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\ConnectedReader;

use Magento\Framework\Model\Context;
use Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Class ConnectedReader
 * @package Magestore\WebposStripeTerminal\Model\ConnectedReader
 */
class ConnectedReader extends \Magento\Framework\Model\AbstractModel implements ConnectedReaderInterface
{

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\WebposStripeTerminal\Model\ResourceModel\ConnectedReader $resource,
        \Magestore\WebposStripeTerminal\Model\ResourceModel\ConnectedReader\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

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
    public function getReaderId()
    {
        return $this->getData(self::READER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setReaderId($value)
    {
        return $this->setData(self::READER_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getReaderLabel()
    {
        return $this->getData(self::READER_LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setReaderLabel($value)
    {
        return $this->setData(self::READER_LABEL, $value);
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
}
