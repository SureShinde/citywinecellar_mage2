<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\Data;
use Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentRequestInterface;

/**
 * Class CreatePaymentIntentRequest
 * @package Magestore\WebposStripeTerminal\Model\Data
 */
class CreatePaymentIntentRequest extends \Magento\Framework\DataObject implements CreatePaymentIntentRequestInterface
{
    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setCurrency($value)
    {
        return $this->setData(self::CURRENCY, $value);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }
}
