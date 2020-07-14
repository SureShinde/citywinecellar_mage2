<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;

use Magestore\Webpos\Api\Data\Config\RefundPaymentTypeInterface;

/**
 * Class RefundPaymentType
 * @package Magestore\Webpos\Model\Config\Data
 */
class RefundPaymentType extends \Magento\Framework\DataObject implements RefundPaymentTypeInterface
{
    /**
     * @inheritdoc
     */
    public function getAcceptedPayments()
    {
        return $this->getData(self::ACCEPTED_PAYMENTS);
    }

    /**
     * @inheritdoc
     */
    public function setAcceptedPayments($value)
    {
        return $this->setData(self::ACCEPTED_PAYMENTS, $value);
    }

    /**
     * @inheritdoc
     */
    public function getUseTransactionPayments()
    {
        return $this->getData(self::USE_TRANSACTION_PAYMENTS);
    }

    /**
     * @inheritdoc
     */
    public function setUseTransactionPayments($value)
    {
        return $this->setData(self::USE_TRANSACTION_PAYMENTS, $value);
    }
}