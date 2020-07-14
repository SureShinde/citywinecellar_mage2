<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Model\Payment\Method;

/**
 * class \Magestore\Payment\Model\Payment\Method\CreditCard
 * 
 * Web POS Credit Card payment method model
 * Methods:
 *  assignData
 *  isAvailable
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class CreditCard extends \Magestore\Payment\Model\Payment\AbstractMethod
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code = 'ccforpos';
    /**
     * @var string
     */
    protected $enabledPath = 'payment/ccforpos/active';
    /**
     * Class of form block
     * @var string
     */
    protected $_formBlockType = 'Magestore\Payment\Block\Payment\Method\CreditCard\CreditCard';
}