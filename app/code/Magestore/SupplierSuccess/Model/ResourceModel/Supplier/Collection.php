<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Supplier;

use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'supplier_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\SupplierSuccess\Model\Supplier', 'Magestore\SupplierSuccess\Model\ResourceModel\Supplier');
    }
}