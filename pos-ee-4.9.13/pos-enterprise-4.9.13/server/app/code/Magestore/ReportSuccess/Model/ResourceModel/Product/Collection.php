<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\ResourceModel\Product;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

/**
 * Class \Magestore\ReportSuccess\Model\ResourceModel\Product\Collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const SOURCE_CODE = "source_code";

    const QTY_ON_HAND = "quantity";

    /**
     * @var array
     */
    protected $mappingField = [
        'supplier' => 'supplier_select.supplier'
    ];

    /**
     * @var array
     */
    protected $mappingWithoutMacField = [
        'supplier' => 'supplier_select.supplier'
    ];

    /**
     * @var array
     */
    protected $mappingFilterField = [];

    /**
     * @var array
     */
    protected $mappingFilterWithoutMacField = [];

    /**
     * @var bool
     */
    protected $isEnabledBarcode = false;

    /**
     * @var string
     */
    protected $barcode = '';

    /**
     * Construct
     */
    protected function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $this->isEnabledBarcode = $scopeConfig->getValue('reportsuccess/general/enable_barcode_in_report');
        $this->barcode = $scopeConfig->getValue('reportsuccess/general/barcode');
        $catalogPriceScope = $scopeConfig->getValue('catalog/price/scope');
        $this->calculateWebsiteScopeConfig($catalogPriceScope);
        if ($this->barcode != 'sku') {
            $attribute = $objectManager->get(\Magento\Catalog\Model\Product\Attribute\Repository::class)
                ->get($this->barcode);
            if ($attribute->getId()) {
                if ($attribute->getScope() == 'global') {
                    $this->setMappingField('barcode', 'at_' . $this->barcode . '.value');
                } else {
                    $this->setMappingField(
                        'barcode',
                        'IF(at_' . $this->barcode . '.value_id > 0, at_' . $this->barcode . '.value, at_'
                        . $this->barcode . '_default.value)'
                    );
                }
            } else {
                $this->setMappingField('barcode', 'sku');
            }
        } else {
            $this->setMappingField('barcode', 'sku');
        }
        parent::_construct();
    }

    /**
     * Calculate website scope config
     *
     * @param string $catalogPriceScope
     */
    public function calculateWebsiteScopeConfig($catalogPriceScope)
    {
        $reportManagement = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $qtyOnHandFieldName = self::QTY_ON_HAND;
            $warehouseIdFieldName = self::SOURCE_CODE;
        } else {
            $qtyOnHandFieldName = 'total_qty';
            $warehouseIdFieldName = 'warehouse_id';
        }

        $qtyOnHandSql = 'IFNULL(SUM(warehouse_product.' . $qtyOnHandFieldName . '),0)';
        $this->mappingField['warehouse'] = 'GROUP_CONCAT(warehouse_product.' . $warehouseIdFieldName . ')';
        $this->mappingWithoutMacField['warehouse'] = 'GROUP_CONCAT(warehouse_product.' . $warehouseIdFieldName . ')';

        if ($catalogPriceScope) {
            $priceSql = 'IF(at_price.value_id > 0, at_price.value, at_price_default.value)';
            $macSql = 'IFNULL(IF(at_mac.value_id > 0, at_mac.value, at_mac_default.value),IF(IF(at_cost.value_id > 0,'.
                ' at_cost.value, at_cost_default.value) is not null,IF(at_cost.value_id > 0, at_cost.value,'.
                ' at_cost_default.value),null))';
            $macWithoutPurchaseSql = 'IF(at_cost.value_id > 0, at_cost.value, at_cost_default.value)';
        } else {
            $priceSql = 'IFNULL(`at_price`.`value`,0)';
            $macSql = 'IFNULL(`at_mac`.`value`,IF(`at_cost`.`value` is not null,at_cost.value,null))';
            $macWithoutPurchaseSql = '`at_cost`.`value`';
        }

        $this->mappingField['qty_on_hand'] = $this->mappingFilterField['qty_on_hand'] = $qtyOnHandSql;
        $this->mappingField['potential_profit'] = $this->mappingFilterField['potential_profit']
            = $priceSql . '*' . $qtyOnHandSql . '-' . $macSql . '*' . $qtyOnHandSql;
        $this->mappingField['stock_value']
            = $this->mappingFilterField['stock_value'] = $macSql . '*' . $qtyOnHandSql;
        $this->mappingField['potential_revenue']
            = $this->mappingFilterField['potential_revenue'] = $priceSql . '*' . $qtyOnHandSql;
        $this->mappingField['potential_margin']
            = $this->mappingFilterField['potential_margin'] = '100*(1 - ' . $macSql . '/' . $priceSql . ')';
        $this->mappingField['price'] = $priceSql;
        $this->mappingField['mac'] = $macSql;

        $this->mappingWithoutMacField['qty_on_hand']
            = $this->mappingFilterWithoutMacField['qty_on_hand'] = $qtyOnHandSql;
        $this->mappingWithoutMacField['potential_profit']
            = $this->mappingFilterWithoutMacField['potential_profit'] = $priceSql . '*' . $qtyOnHandSql . '-'
            . $macWithoutPurchaseSql . '*' . $qtyOnHandSql;
        $this->mappingWithoutMacField['stock_value']
            = $this->mappingFilterWithoutMacField['stock_value'] = $macWithoutPurchaseSql . '*' . $qtyOnHandSql;
        $this->mappingWithoutMacField['potential_revenue']
            = $this->mappingFilterWithoutMacField['potential_revenue'] = $priceSql . '*' . $qtyOnHandSql;
        $this->mappingWithoutMacField['potential_margin']
            = $this->mappingFilterWithoutMacField['potential_margin'] = '100*(1 - ' . $macWithoutPurchaseSql . '/'
            . $priceSql . ')';
        $this->mappingWithoutMacField['price'] = $priceSql;
        $this->mappingWithoutMacField['mac'] = $macWithoutPurchaseSql;
    }

    /**
     * Init Select
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _initSelect()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($this->isEnabledFlat()) {
            $this->getSelect()->from(
                [self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()],
                null
            )->columns(
                ['status' => new \Zend_Db_Expr(ProductStatus::STATUS_ENABLED)]
            );
            $this->addAttributeToSelect($this->getResource()->getDefaultAttributes());
            if ($this->_catalogProductFlatState->getFlatIndexerHelper()->isAddChildData()) {
                $this->getSelect()->where('e.is_child=?', 0);
                $this->addAttributeToSelect(['child_id', 'is_child']);
            }
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }
        $this->addAttributeToSelect('name');
        $this->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $this->addAttributeToFilter(
            \Magento\Catalog\Api\Data\ProductInterface::STATUS,
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        );

        if ($this->isEnabledBarcode && $this->barcode != 'sku') {
            $this->addExpressionAttributeToSelect('barcode', '', $this->barcode);
        }

        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            $this->addExpressionAttributeToSelect(
                'mac',
                'IFNULL({{mac}},IF({{cost}} is not null,at_cost.value,null))',
                ['mac', 'cost']
            );
        } else {
            $this->addExpressionAttributeToSelect('mac', 'IFNULL({{cost}},null)', 'cost');
        }
        $this->addExpressionAttributeToSelect('price', 'IFNULL({{price}},0)', 'price');

        $reportManagement = $objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $groupByField = "sku";
            $this->getSelect()->join(
                ['warehouse_product' => $this->getTable('inventory_source_item')],
                'e.sku = warehouse_product.sku',
                '*'
            );
        } else {
            $groupByField = "product_id";
            $this->getSelect()->join(
                [
                    'warehouse_product' => $this->getTable(
                        \Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Product::MAIN_TABLE
                    )
                ],
                'e.entity_id = warehouse_product.product_id '
                . 'AND warehouse_product.'
                . \Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface::WAREHOUSE_ID
                . '!=' . \Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface::DEFAULT_SCOPE_ID,
                '*'
            );
        }

        if ($objectManager->get(\Magento\Framework\Module\Manager::class)->isEnabled('Magestore_SupplierSuccess')) {
            $supplierSelect = clone $this->getSelect();

            $supplierSelect->reset(\Magento\Framework\DB\Select::FROM);
            $supplierSelect->reset(\Magento\Framework\DB\Select::ORDER);
            $supplierSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
            $supplierSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
            $supplierSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
            $supplierSelect->reset(\Magento\Framework\DB\Select::WHERE);
            $supplierSelect->from(['supplier_product' => $this->getTable('os_supplier_product')]);

            $supplierSelect->columns(['supplier' => 'GROUP_CONCAT(supplier_product.supplier_id)']);
            $supplierSelect->group('supplier_product.product_id');
            $this->getSelect()->joinLeft(
                ['supplier_select' => $supplierSelect],
                'e.entity_id = supplier_select.product_id ',
                []
            );
            $this->getSelect()->columns(
                [
                    'supplier' => new \Zend_Db_Expr($this->getMappingField('supplier'))
                ]
            );

        }

        $this->getSelect()->columns(
            [
                'qty_on_hand' => new \Zend_Db_Expr($this->getMappingField('qty_on_hand')),
                'potential_profit' => new \Zend_Db_Expr($this->getMappingField('potential_profit')),
                'stock_value' => new \Zend_Db_Expr($this->getMappingField('stock_value')),
                'potential_revenue' => new \Zend_Db_Expr($this->getMappingField('potential_revenue')),
                'potential_margin' => new \Zend_Db_Expr(
                    'FORMAT(' . $this->getMappingField('potential_margin') . ',2)'
                ),
                'warehouse' => new \Zend_Db_Expr($this->getMappingField('warehouse'))
            ]
        );

        if ($this->isEnabledBarcode && $this->barcode != 'sku') {
            $this->getSelect()->columns(
                ['barcode' => new \Zend_Db_Expr($this->getMappingField('barcode'))]
            );
        }

        $this->getSelect()->group("warehouse_product." . $groupByField);

        return $this;
    }

    /**
     * Get mapping field
     *
     * @param string|null $key
     * @return array|mixed
     */
    public function getMappingField($key = null)
    {
        if (!$key) {
            if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
                return $this->mappingField;
            }
            return $this->mappingWithoutMacField;
        }
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            return $this->mappingField[$key];
        }
        return $this->mappingWithoutMacField[$key];
    }

    /**
     * Set mapping field
     *
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function setMappingField($key, $value)
    {
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            return $this->mappingField[$key] = $value;
        }
        return $this->mappingWithoutMacField[$key] = $value;
    }

    /**
     * Filters products into a collection
     *
     * @param int $warehouseId
     * @return $this
     */
    public function addWarehouseToFilter($warehouseId)
    {
        $reportManagement = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $warehouseIdFieldName = self::SOURCE_CODE;
        } else {
            $warehouseIdFieldName = \Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface::WAREHOUSE_ID;
        }
        $this->getSelect()->where(
            $this->getConnection()->prepareSqlCondition(
                'warehouse_product.' . $warehouseIdFieldName,
                ['in' => $warehouseId]
            )
        );
        return $this;
    }

    /**
     * Add supplier to filter
     *
     * @param int $supplierId
     * @return $this
     */
    public function addSupplierToFilter($supplierId)
    {
        if ($supplierId == \Magestore\ReportSuccess\Model\Source\Adminhtml\SupplierWithNone::NONE_VALUE) {
            $this->getSelect()->where(
                $this->getConnection()->prepareSqlCondition(
                    'supplier_select.supplier',
                    ['null' => '']
                )
            );
        } else {
            $this->getSelect()->where(
                $this->getConnection()->prepareSqlCondition(
                    'supplier_select.supplier',
                    ['finset' => [$supplierId]]
                )
            );
        }
        return $this;
    }

    /**
     * Filters barcode into a collection
     *
     * @param string $barcode
     * @return $this
     */
    public function addBarcodeToFilter($barcode)
    {
        $this->getSelect()->where(
            $this->getConnection()->prepareSqlCondition(
                $this->getMappingField('barcode'),
                ['like' => '%' . $barcode . '%']
            )
        );
        return $this;
    }

    /**
     * Get Select count sql
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Zend_Db_Select_Exception
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        if (!count($this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP))) {
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }
        $countSelect->reset(\Magento\Framework\DB\Select::HAVING);
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT " . implode(", ", $group) . ")")));
        return $countSelect;
    }

    /**
     * Add field to filter
     *
     * @param mixed $field
     * @param array $condition
     * @return $this|\Magento\Framework\Data\Collection\AbstractDb
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            foreach ($this->mappingFilterField as $key => $value) {
                if ($field == $key) {
                    $field = $value;
                    return $this->addFieldToFilterCallBack($field, $condition);
                }
            }
        } else {
            foreach ($this->mappingFilterWithoutMacField as $key => $value) {
                if ($field == $key) {
                    $field = $value;
                    return $this->addFieldToFilterCallBack($field, $condition);
                }
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add order
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach ($this->getMappingField() as $key => $value) {
            if ($field == $key) {
                $field = $value;
                $this->getSelect()->order(new \Zend_Db_Expr($field . ' ' . $direction));
                return $this;
            }
        }

        return parent::addOrder($field, $direction);
    }

    /**
     * Add field to filter call back
     *
     * @param mixed $field
     * @param array $condition
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function addFieldToFilterCallBack($field, $condition)
    {
        foreach ($condition as $con => $value) {
            $conditionSql = $this->_getConditionSql($field, $condition);
            $this->getSelect()->having($conditionSql);
        }
    }
}
