<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Model\Payment;

/**
 * Class PaymentType
 * @package Magestore\Payment\Model\Payment
 */
class PaymentType
{
    /**
     * @var Type\CreditCardFormPayments
     */
    protected $creditCardFormPayments;
    /**
     * @var Type\EwalletPayments
     */
    protected $ewalletPayments;
    /**
     * @var Type\FlatPayments
     */
    protected $flatPayments;
    /**
     * @var Type\InternetTerminalPayments
     */
    protected $internetTerminalPayments;
    /**
     * @var Type\PreventCancelOrderRulePayments
     */
    protected $preventCancelOrderRulePayments;
    /**
     * @var Type\TerminalPayments
     */
    protected $terminalPayments;

    /**
     * PaymentType constructor.
     * @param Type\CreditCardFormPayments $creditCardFormPayments
     * @param Type\EwalletPayments $ewalletPayments
     * @param Type\FlatPayments $flatPayments
     * @param Type\InternetTerminalPayments $internetTerminalPayments
     * @param Type\PreventCancelOrderRulePayments $preventCancelOrderRulePayments
     * @param Type\TerminalPayments $terminalPayments
     */
    public function __construct(
        \Magestore\Payment\Model\Payment\Type\CreditCardFormPayments $creditCardFormPayments,
        \Magestore\Payment\Model\Payment\Type\EwalletPayments $ewalletPayments,
        \Magestore\Payment\Model\Payment\Type\FlatPayments $flatPayments,
        \Magestore\Payment\Model\Payment\Type\InternetTerminalPayments $internetTerminalPayments,
        \Magestore\Payment\Model\Payment\Type\PreventCancelOrderRulePayments $preventCancelOrderRulePayments,
        \Magestore\Payment\Model\Payment\Type\TerminalPayments $terminalPayments
    )
    {
        $this->creditCardFormPayments = $creditCardFormPayments;
        $this->ewalletPayments = $ewalletPayments;
        $this->flatPayments = $flatPayments;
        $this->internetTerminalPayments = $internetTerminalPayments;
        $this->preventCancelOrderRulePayments = $preventCancelOrderRulePayments;
        $this->terminalPayments = $terminalPayments;
    }

    /**
     * @return array
     */
    public function getData() {
        $result = [];
        $result['credit_card_form_payments'] = $this->creditCardFormPayments->getData();
        $result['e_wallet_payments'] = $this->ewalletPayments->getData();
        $result['flat_payments'] = $this->flatPayments->getData();
        $result['internet_terminal_payments'] = $this->internetTerminalPayments->getData();
        $result['prevent_cancel_order_rule_payments'] = $this->preventCancelOrderRulePayments->getData();
        $result['terminal_payments'] = $this->terminalPayments->getData();
        return $result;
    }
}
