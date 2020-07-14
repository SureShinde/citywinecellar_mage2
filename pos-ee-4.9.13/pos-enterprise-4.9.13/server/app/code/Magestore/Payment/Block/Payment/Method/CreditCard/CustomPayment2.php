<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Payment\Block\Payment\Method\CreditCard;


/**
 * class \Magestore\Payment\Block\Payment\Method\Cc\Info\Cp2
 * 
 * CP2 for POS info block
 * Methods:
 *  _construct
 *  _prepareSpecificInformation
 *  getMethodTitle
 * 
 * @category    Magestore
 * @package     Magestore\Payment\Block\Payment\Method\Cc\Info
 * @module      Webpos
 * @author      Magestore Developer
 */
class CustomPayment2 extends \Magestore\Payment\Block\Payment\Method\ReferencePaymentAbstract
{
    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        return $this->getCustomPayment2MethodTitle();
    }

}