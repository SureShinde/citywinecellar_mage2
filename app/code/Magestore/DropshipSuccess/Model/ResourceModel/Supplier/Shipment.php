<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\ResourceModel\Supplier;

/**
 * Class Shipment
 * @package Magestore\DropshipSuccess\Model\ResourceModel\Supplier
 */
class Shipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_dropship_supplier_shipment', 'supplier_shipment_id');
    }
}