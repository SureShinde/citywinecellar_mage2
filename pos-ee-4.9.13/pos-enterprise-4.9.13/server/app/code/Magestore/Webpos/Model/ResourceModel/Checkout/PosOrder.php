<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Checkout;

/**
 * Class PosOrder
 *
 * @package Magestore\Webpos\Model\ResourceModel\Checkout
 */
class PosOrder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webpos_order', 'id');
    }
}
