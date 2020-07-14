<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive;

/**
 * Class Collection
 * @package Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\TransferStock\Model\InventoryTransfer\Receive', 'Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive');
    }
}
