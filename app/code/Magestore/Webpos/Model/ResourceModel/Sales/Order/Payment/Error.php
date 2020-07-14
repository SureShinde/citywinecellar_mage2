<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment;

/**
 * Class Error
 *
 * @package Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment
 */
class Error extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webpos_order_payment_error', 'id');
    }
}
