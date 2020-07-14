<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Supplier;

/**
 * Class Product
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier
 */
class PricingList extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_supplier_pricinglist', 'supplier_pricinglist_id');
    }

    /**
     *
     * @return array = [
     *  'supplier_id' => int,
     *  'product_id' => int,
     *  'product_sku' => string,
     *  'product_name' => string,
     *  'product_supplier_sku' => string,
     *  'minimal_qty' => decimal,
     *  'cost' => float,
     *  'start_date' => date format Y-m-d
     *  'end_date' => date format Y-m-d
     * ]
     */
    public function addPricingList($data)
    {
        $connection = $this->getConnection();
        $table = $this->getTable('os_supplier_pricinglist');
        $connection->insertOnDuplicate($table, $data);
    }
}
