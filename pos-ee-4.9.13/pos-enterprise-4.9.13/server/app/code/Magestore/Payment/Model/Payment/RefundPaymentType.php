<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Model\Payment;

use Magestore\Payment\Model\Payment\RefundType\AcceptedPayments;
use Magestore\Payment\Model\Payment\RefundType\UseTransactionPayments;

/**
 * Class RefundPaymentType
 * @package Magestore\Payment\Model\Payment
 */
class RefundPaymentType
{
    /**
     * @var AcceptedPayments
     */
    protected $acceptedPayments;
    /**
     * @var UseTransactionPayments
     */
    protected $useTransactionPayments;


    /**
     * RefundPaymentType constructor.
     * @param AcceptedPayments $acceptedPayments
     * @param UseTransactionPayments $useTransactionPayments
     */
    public function __construct(
        AcceptedPayments $acceptedPayments,
        UseTransactionPayments $useTransactionPayments
    )
    {
        $this->acceptedPayments = $acceptedPayments;
        $this->useTransactionPayments = $useTransactionPayments;
    }

    /**
     * @return array
     */
    public function getAcceptedPayments() {
        return $this->acceptedPayments->getData();
    }

    /**
     * @return array
     */
    public function getUseTransactionPayments() {
        return $this->useTransactionPayments->getData();
    }
}
