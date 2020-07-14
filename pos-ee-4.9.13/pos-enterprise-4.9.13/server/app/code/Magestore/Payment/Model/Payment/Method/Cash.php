<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Model\Payment\Method;

/**
 * class \Magestore\Payment\Model\Payment\Method\Cash
 * 
 * Web POS Cash payment method model
 * Methods:
 *  isAvailable
 * 
 * @category    Magestore
 * @package     Magestore_Payment
 * @module      Payment
 * @author      Magestore Developer
 */
class Cash extends \Magestore\Payment\Model\Payment\AbstractMethod
{

    /**
     * Payment method code
     * @var string
     */
    protected $_code = 'cashforpos';

    /**
     * @var string
     */
    protected $enabledPath = 'payment/cashforpos/active';

    /**
     * Class of form block
     * @var string
     */
    protected $_formBlockType = 'Magestore\Payment\Block\Payment\Method\Cash';
}
