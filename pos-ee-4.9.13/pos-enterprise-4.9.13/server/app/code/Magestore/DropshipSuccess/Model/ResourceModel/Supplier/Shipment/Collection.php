<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment;

use Magento\Framework\DB\Select;

/**
 * Class DropshipRequest
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'supplier_shipment_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\Supplier\Shipment', 'Magestore\DropshipSuccess\Model\ResourceModel\Supplier\Shipment');
    }
}