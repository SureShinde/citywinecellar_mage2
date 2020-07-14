<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\Data;
use Magestore\WebposStripeTerminal\Api\Data\ConnectionTokenResponseInterface;

/**
 * Class ConnectionTokenResponse
 * @package Magestore\WebposStripeTerminal\Model\Data
 */
class ConnectionTokenResponse extends \Magento\Framework\DataObject implements ConnectionTokenResponseInterface
{
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
    public function getSecret()
    {
        return $this->getData(self::SECRET);
    }

    /**
     * @inheritdoc
     */
    public function setSecret($value)
    {
        return $this->setData(self::SECRET, $value);
    }
}
