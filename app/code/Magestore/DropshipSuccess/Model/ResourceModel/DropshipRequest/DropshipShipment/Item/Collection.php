<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Item;

use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Item
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'dropship_shipment_item_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment\Item', 
            'Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Item'
        );
    }
}