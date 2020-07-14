<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline;

/**
 * Class Collection
 * @package Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'payment_offline_id';

    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\PaymentOffline\Model\PaymentOffline', 'Magestore\PaymentOffline\Model\ResourceModel\PaymentOffline');
    }
}