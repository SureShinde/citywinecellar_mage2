<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * Interface RefundPaymentTypeInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface RefundPaymentTypeInterface
{
    const ACCEPTED_PAYMENTS = 'accepted_payments';
    const USE_TRANSACTION_PAYMENTS = 'use_transaction_payments';
    /**
     * @return string
     */
    public function getAcceptedPayments();

    /**
     * @param string $value
     * @return RefundPaymentTypeInterface
     */
    public function setAcceptedPayments($value);
    /**
     * @return string
     */
    public function getUseTransactionPayments();

    /**
     * @param string $value
     * @return RefundPaymentTypeInterface
     */
    public function setUseTransactionPayments($value);
}