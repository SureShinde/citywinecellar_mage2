<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer;

/**
 * Class ReceiveProduct
 * @package Magestore\TransferStock\Model\ResourceModel\InventoryTransfer
 */
class ReceiveProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('os_inventorytransfer_receive_product', 'receive_product_id');
    }
}
