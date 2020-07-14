<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment;

/**
 * Class Item
 * @package Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment
 */
class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_dropship_shipment_item', 'dropship_shipment_item_id');
    }
}