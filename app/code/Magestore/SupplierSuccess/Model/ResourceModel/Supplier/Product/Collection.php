<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product;

use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'supplier_product_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\SupplierSuccess\Model\Supplier\Product', 'Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product');
    }
}