<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment;

use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'dropship_shipment_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment', 
            'Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment'
        );
    }
}