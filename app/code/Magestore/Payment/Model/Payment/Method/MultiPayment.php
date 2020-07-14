<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Model\Payment\Method;

/**
 * class \Magestore\Payment\Model\Payment\Method\MultiPayment
 * 
 * Web POS partial payment method model
 * Methods:
 *  assignData
 *  isAvailable
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class MultiPayment extends \Magestore\Payment\Model\Payment\AbstractMethod
{

    const PAYMENT_CODE = 'multipaymentforpos';
    /**
     * Payment method code
     * @var string
     */
    protected $_code = 'multipaymentforpos';

    /**
     * @var string
     */
    protected $enabledPath = 'payment/multipaymentforpos/active';
    /**
     * Class of form block
     * @var string
     */
    protected $_infoBlockType = 'Magestore\Payment\Block\Payment\Method\MultiPayment';
}