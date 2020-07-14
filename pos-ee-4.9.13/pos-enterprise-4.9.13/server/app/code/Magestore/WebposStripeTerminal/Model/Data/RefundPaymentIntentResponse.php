<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Model\Data;
use Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface;

/**
 * Class RefundPaymentIntentResponse
 * @package Magestore\WebposStripeTerminal\Model\Data
 */
class RefundPaymentIntentResponse extends \Magento\Framework\DataObject implements RefundPaymentIntentResponseInterface
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
    public function getBalanceTransaction()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBalanceTransaction($value)
    {
        return $this->setData(self::BALANCE_TRANSACTION, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCharge()
    {
        return $this->getData(self::CHARGE);
    }

    /**
     * @inheritdoc
     */
    public function setCharge($value)
    {
        return $this->setData(self::CHARGE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCreated()
    {
        return $this->getData(self::CREATED);
    }

    /**
     * @inheritdoc
     */
    public function setCreated($value)
    {
        return $this->setData(self::CREATED, $value);
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
    public function getReason()
    {
        return $this->getData(self::REASON);
    }

    /**
     * @inheritdoc
     */
    public function setReason($value)
    {
        return $this->setData(self::REASON, $value);
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
