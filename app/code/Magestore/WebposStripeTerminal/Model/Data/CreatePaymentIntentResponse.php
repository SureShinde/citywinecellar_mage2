<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\Data;
use Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentResponseInterface;

/**
 * Class CreatePaymentIntentResponse
 * @package Magestore\WebposStripeTerminal\Model\Data
 */
class CreatePaymentIntentResponse extends \Magento\Framework\DataObject implements CreatePaymentIntentResponseInterface
{
    /**
     * @inheritdoc
     */
    public function getIntent()
    {
        return $this->getData(self::INTENT);
    }

    /**
     * @inheritdoc
     */
    public function setIntent($value)
    {
        return $this->setData(self::INTENT, $value);
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
