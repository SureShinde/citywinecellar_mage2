<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * @api
 */
/**
 * Interface ConfigInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface PaymentInterface
{
    const PAYMENT_METHODS = 'payment_methods';

    /**
     * @return string
     */
    public function getPaymentMethods();

    /**
     * @param string $paymentMethod
     * @return PaymentInterface
     */
    public function setPaymentMethods($paymentMethod);
}