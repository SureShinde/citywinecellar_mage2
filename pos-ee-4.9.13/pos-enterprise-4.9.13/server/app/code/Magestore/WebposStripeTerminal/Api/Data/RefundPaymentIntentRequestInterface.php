<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface RefundPaymentIntentRequestInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface RefundPaymentIntentRequestInterface
{
    const AMOUNT = 'amount';
    const PAYMENT_INTENT_ID = 'payment_intent_id';

    /**
     * @return float|string|null
     */
    public function getAmount();

    /**
     * @param float|string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentRequestInterface
     */
    public function setAmount($value);

    /**
     * @return string|float|null
     */
    public function getPaymentIntentId();

    /**
     * @param string|float|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentRequestInterface
     */
    public function setPaymentIntentId($value);
}