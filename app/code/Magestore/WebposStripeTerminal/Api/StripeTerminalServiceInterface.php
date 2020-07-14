<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api;

/**
 * Interface StripeTerminalServiceInterface
 * @package Magestore\WebposStripeTerminal\Api
 */
interface StripeTerminalServiceInterface
{
    const CODE = 'stripeterminal_integration';
    const TITLE = 'Stripe Verifone P400';
    const ONLINE_CODE = 'stripe';
    const CONFIG_PATH = 'webpos/payment/stripeterminal';

    /**
     * API get token from stripe
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectionTokenResponseInterface
     */
    public function connectionToken();
    /**
     * API create payment intent which auth card, no charge
     *
     * @param  \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentRequestInterface
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\CreatePaymentIntentResponseInterface
     */
    public function createPaymentIntent($request);
    /**
     * API transfer customer money to merchant
     *
     * @param  \Magestore\WebposStripeTerminal\Api\Data\CapturePaymentIntentRequestInterface $request
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\CapturePaymentIntentResponseInterface
     */
    public function capturePaymentIntent($request);
    /**
     * API register new reader
     *
     * @param  \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderRequestInterface $request
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\RegisterReaderResponseInterface
     */
    public function registerReader($request);
    /**
     * API refund payment intent
     *
     * @param  \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentRequestInterface
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\RefundPaymentIntentResponseInterface
     */
    public function refundPaymentIntent($request);

    /**
     * API save connected reader
     *
     * @param  \Magestore\WebposStripeTerminal\Api\Data\SaveConnectedReaderRequestInterface
     *
     * @return \Magestore\WebposStripeTerminal\Api\Data\SaveConnectedReaderResponseInterface
     */
    public function saveConnectedReader($request);

    /**
     * Validate env
     *
     * @return bool
     */
    public function validateEnv();

    /**
     * Test connect stripe API
     *
     * @return bool
     */
    public function connectToApi();

    /**
     *  Get linked stripe Location id
     *
     * @param  string|null $secret
     * @return bool|string
     */
    public function getStripeLocationId($secret = null);
}