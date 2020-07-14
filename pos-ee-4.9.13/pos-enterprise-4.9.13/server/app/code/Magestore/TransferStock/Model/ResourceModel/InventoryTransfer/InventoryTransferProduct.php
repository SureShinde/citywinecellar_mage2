<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer;

/**
 * Class Collection
 * @package Magestore\TransferStock\Model\ResourceModel\InventoryTransfer
 */
class InventoryTransferProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('os_inventorytransfer_product', 'entity_id');
    }
}
