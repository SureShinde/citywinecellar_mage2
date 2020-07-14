<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer;

/**
 * Class Receive
 * @package Magestore\TransferStock\Model\ResourceModel\InventoryTransfer
 */
class Receive extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('os_inventorytransfer_receive', 'receive_id');
    }
}
