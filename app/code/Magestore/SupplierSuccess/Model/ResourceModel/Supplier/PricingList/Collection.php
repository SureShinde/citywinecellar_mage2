<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList;

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
    protected $_idFieldName = 'supplier_pricinglist_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\SupplierSuccess\Model\Supplier\PricingList', 'Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList');
    }

    /**
     * @param $productSku
     * @param $supplierId
     * @param null $time
     * @return array
     */
    public function getProductCost($productSku = null, $supplierId, $time = null)
    {
        if (!$time) {
            /** @var \Magento\Framework\Stdlib\DateTime\DateTime $dateTime */
            $dateTime = \Magento\Framework\App\ObjectManager::getInstance()->create(
                '\Magento\Framework\Stdlib\DateTime\DateTime'
            );
            $time = $dateTime->gmtDate('Y-m-d');
        }
        $from = $time. ' 23:59:59';
        $end = $time. ' 00:00:00';
        $select = $this->getConnection()->select()->from(['main_table' => $this->getMainTable()]);
        $orWhereConditions = [
            "(main_table.start_date <= '{$from}' and main_table.end_date >= '{$end}')",
            "(main_table.start_date <= '{$from}' and main_table.end_date is null)",
            "(main_table.start_date is null and main_table.end_date >= '{$end}')",
            "(main_table.start_date is null and main_table.end_date is null)"
        ];
        if ($productSku) {
            $andWhereConditions = [
                "main_table.supplier_id = '{$supplierId}'",
                "main_table.product_sku = '{$productSku}')",
                "main_table.cost > 0"
            ];
        }
        if (!$productSku) {
            $andWhereConditions = [
                "main_table.supplier_id = '{$supplierId}'",
                "main_table.cost > 0"
            ];
        }
        $orWhereCondition = implode(' OR ', $orWhereConditions);
        $andWhereCondition = implode(' AND ', $andWhereConditions);
        $select->where('(' . $orWhereCondition . ') AND ' . $andWhereCondition);
        $select->joinLeft(
            ["catalog_product" => $this->getTable('catalog_product_entity')],
            "main_table.product_sku = catalog_product.sku",
            ['product_id' => 'catalog_product.entity_id']
        );
        $select->order('main_table.minimal_qty ASC');
        $result = $this->getConnection()->fetchAll($select);
        return $result;
    }
}