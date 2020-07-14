<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface CreatePaymentIntentRequestInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface CreatePaymentIntentRequestInterface
{
    const AMOUNT = 'amount';
    const CURRENCY = 'currency';
    const DESCRIPTION = 'description';

    /**
     * @return float|string|null
     */
    public function getAmount();

    /**
     * @param float|string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentRequestInterface
     */
    public function setAmount($value);

    /**
     * @return string|null
     */
    public function getCurrency();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentRequestInterface
     */
    public function setCurrency($value);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentRequestInterface
     */
    public function setDescription($value);
}