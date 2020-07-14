<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Block\Payment\Method\CreditCard;

/**
 * Class CreditCard
 * @package Magestore\Payment\Block\Payment\Method\Cc\Info
 */
class CreditCard extends \Magestore\Payment\Block\Payment\Method\ReferencePaymentAbstract
{
    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        return $this->getCreditCardMethodTitle();
    }

}