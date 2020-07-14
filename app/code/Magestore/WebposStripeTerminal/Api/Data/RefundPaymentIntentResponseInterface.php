<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface RefundPaymentIntentResponseInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface RefundPaymentIntentResponseInterface
{
    const ID = 'id';
    const OBJECT = 'object';
    const AMOUNT = 'amount';
    const BALANCE_TRANSACTION = 'balance_transaction';
    const CHARGE = 'charge';
    const CREATED = 'created';
    const CURRENCY = 'currency';
    const STATUS = 'status';
    const REASON = 'reason';

    /**
     * @return float|string|null
     */
    public function getAmount();

    /**
     * @param float|string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setAmount($value);

    /**
     * @return string|float|null
     */
    public function getId();

    /**
     * @param string|float|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setId($value);

    /**
     * @return string|float|null
     */
    public function getObject();

    /**
     * @param string|float|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setObject($value);

    /**
     * @return float|string|null
     */
    public function getBalanceTransaction();

    /**
     * @param float|string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setBalanceTransaction($value);

    /**
     * @return string|float|null
     */
    public function getCharge();

    /**
     * @param string|float|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setCharge($value);

    /**
     * @return float|string|null
     */
    public function getCreated();

    /**
     * @param float|string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setCreated($value);

    /**
     * @return string|float|null
     */
    public function getCurrency();

    /**
     * @param string|float|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setCurrency($value);

    /**
     * @return string|float|null
     */
    public function getReason();

    /**
     * @param string|float|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setReason($value);


    /**
     * @return string|float|null
     */
    public function getStatus();

    /**
     * @param string|float|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function setStatus($value);
}