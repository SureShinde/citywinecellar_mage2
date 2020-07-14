<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

/**
 * Interface PaymentTypeInterface
 * @package Magestore\Webpos\Api\Data\Config
 */
interface PaymentTypeInterface
{
    const CREDIT_CARD_FORM_PAYMENTS = 'credit_card_form_payments';
    const E_WALLET_PAYMENTS = 'e_wallet_payments';
    const FLAT_PAYMENTS = 'flat_payments';
    const INTERNET_TERMINAL_PAYMENTS = 'internet_terminal_payments';
    const TERMINAL_PAYMENT = 'terminal_payments';
    const PREVENT_CANCEL_ORDER_RULE_PAYMENTS = 'prevent_cancel_order_rule_payments';

    /**
     * @return string
     */
    public function getCreditCardFormPayments();

    /**
     * @param string $creditCardFormPayments
     * @return PaymentTypeInterface
     */
    public function setCreditCardFormPayments($creditCardFormPayments);
    /**
     * @return string
     */
    public function getEWalletPayments();

    /**
     * @param string $eWalletPayment
     * @return PaymentTypeInterface
     */
    public function setEWalletPayments($eWalletPayment);
    /**
     * @return string
     */
    public function getFlatPayments();

    /**
     * @param string $flatPayments
     * @return PaymentTypeInterface
     */
    public function setFlatPayments($flatPayments);
    /**
     * @return string
     */
    public function getInternetTerminalPayments();

    /**
     * @param string $internetTerminalPayments
     * @return PaymentTypeInterface
     */
    public function setInternetTerminalPayments($internetTerminalPayments);
    /**
     * @return string
     */
    public function getTerminalPayments();

    /**
     * @param string $terminalPayments
     * @return PaymentTypeInterface
     */
    public function setTerminalPayments($terminalPayments);
    /**
     * @return string
     */
    public function getPreventCancelOrderRulePayments();

    /**
     * @param string $preventCancelOrderRulePayments
     * @return PaymentTypeInterface
     */
    public function setPreventCancelOrderRulePayments($preventCancelOrderRulePayments);
}