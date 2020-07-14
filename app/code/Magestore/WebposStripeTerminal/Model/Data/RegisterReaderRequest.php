<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\Data;
use Magestore\WebposStripeTerminal\Api\Data\RegisterReaderRequestInterface;

/**
 * Class RegisterReaderRequest
 * @package Magestore\WebposStripeTerminal\Model\Data
 */
class RegisterReaderRequest extends \Magento\Framework\DataObject implements RegisterReaderRequestInterface
{
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
    public function getRegistrationCode()
    {
        return $this->getData(self::REGISTRATION_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setRegistrationCode($value)
    {
        return $this->setData(self::REGISTRATION_CODE, $value);
    }
}
