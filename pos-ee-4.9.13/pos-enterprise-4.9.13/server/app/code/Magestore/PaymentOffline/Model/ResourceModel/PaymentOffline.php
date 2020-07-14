<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Model\ResourceModel;

/**
 * Class PaymentOffline
 * @package Magestore\PaymentOffline\Model\ResourceModel
 */
class PaymentOffline extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('webpos_payment_offline', 'payment_offline_id');
    }
}