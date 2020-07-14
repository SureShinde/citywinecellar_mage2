<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\Data;
use Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentRequestInterface;

/**
 * Class RefundPaymentIntentRequest
 * @package Magestore\WebposStripeTerminal\Model\Data
 */
class RefundPaymentIntentRequest extends \Magento\Framework\DataObject implements RefundPaymentIntentRequestInterface
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
    public function getPaymentIntentId()
    {
        return $this->getData(self::PAYMENT_INTENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentIntentId($value)
    {
        return $this->setData(self::PAYMENT_INTENT_ID, $value);
    }
}
