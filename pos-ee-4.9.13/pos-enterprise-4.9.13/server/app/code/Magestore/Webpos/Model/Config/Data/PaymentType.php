<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;
use Magestore\Webpos\Api\Data\Config\PaymentTypeInterface;

/**
 * Class PaymentType
 * @package Magestore\Webpos\Model\Config\Data
 */
class PaymentType extends \Magento\Framework\DataObject implements PaymentTypeInterface
{
    /**
     * @return string
     */
    public function getCreditCardFormPayments() {
        return $this->getData(self::CREDIT_CARD_FORM_PAYMENTS);
    }

    /**
     * @param string $creditCardFormPayments
     * @return PaymentTypeInterface
     */
    public function setCreditCardFormPayments($creditCardFormPayments) {
        return $this->setData(self::CREDIT_CARD_FORM_PAYMENTS, $creditCardFormPayments);
    }
    /**
     * @return string
     */
    public function getEWalletPayments() {
        return $this->getData(self::E_WALLET_PAYMENTS);
    }

    /**
     * @param string $eWalletPayment
     * @return PaymentTypeInterface
     */
    public function setEWalletPayments($eWalletPayment) {
        return $this->setData(self::E_WALLET_PAYMENTS, $eWalletPayment);
    }
    /**
     * @return string
     */
    public function getFlatPayments() {
        return $this->getData(self::FLAT_PAYMENTS);
    }

    /**
     * @param string $flatPayments
     * @return PaymentTypeInterface
     */
    public function setFlatPayments($flatPayments) {
        return $this->setData(self::FLAT_PAYMENTS, $flatPayments);
    }
    /**
     * @return string
     */
    public function getInternetTerminalPayments() {
        return $this->getData(self::INTERNET_TERMINAL_PAYMENTS);
    }

    /**
     * @param string $internetTerminalPayments
     * @return PaymentTypeInterface
     */
    public function setInternetTerminalPayments($internetTerminalPayments) {
        return $this->setData(self::INTERNET_TERMINAL_PAYMENTS, $internetTerminalPayments);
    }
    /**
     * @return string
     */
    public function getTerminalPayments() {
        return $this->getData(self::TERMINAL_PAYMENT);
    }

    /**
     * @param string $terminalPayments
     * @return PaymentTypeInterface
     */
    public function setTerminalPayments($terminalPayments) {
        return $this->setData(self::TERMINAL_PAYMENT, $terminalPayments);
    }
    /**
     * @return string
     */
    public function getPreventCancelOrderRulePayments() {
        return $this->getData(self::PREVENT_CANCEL_ORDER_RULE_PAYMENTS);
    }

    /**
     * @param string $preventCancelOrderRulePayments
     * @return PaymentTypeInterface
     */
    public function setPreventCancelOrderRulePayments($preventCancelOrderRulePayments) {
        return $this->setData(self::PREVENT_CANCEL_ORDER_RULE_PAYMENTS, $preventCancelOrderRulePayments);
    }
}