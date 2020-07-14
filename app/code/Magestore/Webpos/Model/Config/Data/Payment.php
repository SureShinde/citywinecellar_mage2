<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Data;
/**
 * Class SystemConfig
 * @package Magestore\Webpos\Model\Config\Data
 */
class Payment extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Config\PaymentInterface
{
    /**
     * @inheritdoc
     */
    public function getPaymentMethods(){
        return $this->getData(self::PAYMENT_METHODS);
    }

    /**
     * @inheritdoc
     */
    public function setPaymentMethods($paymentMethod){
        return $this->setData(self::PAYMENT_METHODS, $paymentMethod);
    }
}