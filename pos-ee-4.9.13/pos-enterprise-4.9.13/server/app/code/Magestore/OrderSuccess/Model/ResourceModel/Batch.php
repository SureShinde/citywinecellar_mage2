<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\ResourceModel;

/**
 * Class Batch
 * @package Magestore\OrderSuccess\Model\ResourceModel
 */
class Batch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_ordersuccess_batch', 'batch_id');
    }
}
