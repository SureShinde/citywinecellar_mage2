<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface CapturePaymentIntentRequestInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface CapturePaymentIntentRequestInterface
{
    const PAYMENT_INTENT_ID = 'payment_intent_id';

    /**
     * @return string|null
     */
    public function getPaymentIntentId();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\CapturePaymentIntentRequestInterface
     */
    public function setPaymentIntentId($value);
}