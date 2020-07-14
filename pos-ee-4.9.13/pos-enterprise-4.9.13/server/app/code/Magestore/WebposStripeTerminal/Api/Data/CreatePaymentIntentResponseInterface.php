<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface CreatePaymentIntentResponseInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface CreatePaymentIntentResponseInterface
{
    const INTENT = 'intent';
    const SECRET = 'secret';

    /**
     * @return string|null
     */
    public function getIntent();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentResponseInterface
     */
    public function setIntent($value);

    /**
     * @return string|null
     */
    public function getSecret();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentRequestInterface
     */
    public function setSecret($value);
}