<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment;

/**
 * Class Collection
 * @package Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\Checkout\Order\Payment::class, \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment::class);
    }
}