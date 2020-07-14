<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\ResourceModel\Report\IncomingStock;

use Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Product;
use Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder;

/**
 * Class \Magestore\ReportSuccess\Model\ResourceModel\Report\IncomingStock\Collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @var array
     */
    protected $mappingField = [
        'product_name' => 'po_item.product_name',
        'supplier' => 'GROUP_CONCAT(DISTINCT at_supplier.supplier_id)',
        'purchase_order' => 'GROUP_CONCAT(DISTINCT po.purchase_order_id)',
        'incoming_stock' => 'IFNULL(SUM(at_incoming_stock.incoming_stock),0)',
        'overdue_incoming_stock' => 'IFNULL(SUM(at_overdue_incoming_stock.overdue_incoming_stock),0)',
        'total_cost' => 'IFNULL(SUM(at_incoming_stock.total_cost/po.currency_rate),0)',
        'qty_on_hand' => 'warehouse_product.qty_on_hand'
    ];

    /**
     * @var array
     */
    protected $mappingFilterField = [
        'product_name' => 'po_item.product_name',
        'supplier' => 'GROUP_CONCAT(DISTINCT at_supplier.supplier_id)',
        'purchase_order' => 'GROUP_CONCAT(DISTINCT po.purchase_order_id)',
        'incoming_stock' => 'IFNULL(SUM(at_incoming_stock.incoming_stock),0)',
        'overdue_incoming_stock' => 'IFNULL(SUM(at_overdue_incoming_stock.overdue_incoming_stock),0)',
        'total_cost' => 'IFNULL(SUM(at_incoming_stock.total_cost/po.currency_rate),0)',
        'qty_on_hand' => 'warehouse_product.qty_on_hand'
    ];

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
        $reportManagement = $objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $this->mappingField['qty_on_hand'] = 'warehouse_product.quantity';
            $this->mappingFilterField['qty_on_hand'] = 'warehouse_product.quantity';
        }

        $this->isEnabledBarcode = $objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->getValue('reportsuccess/general/enable_barcode_in_report');
        $this->barcode = $objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->getValue('reportsuccess/general/barcode');
        if ($this->barcode != 'sku') {
            $attribute = $objectManager->get(\Magento\Catalog\Model\Product\Attribute\Repository::class)
                ->get($this->barcode);
            if ($attribute->getId()) {
                if ($attribute->getScope() == 'global') {
                    $this->setMappingField('barcode', 'at_' . $this->barcode . '.value');
                } else {
                    $this->setMappingField(
                        'barcode',
                        'IF(at_' . $this->barcode . '.value_id > 0, at_'
                        . $this->barcode . '.value, at_' . $this->barcode . '_default.value)'
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
     * Init select
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _initSelect()
    {
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

        if ($this->isEnabledBarcode && $this->barcode != 'sku') {
            $this->addExpressionAttributeToSelect('barcode', '', $this->barcode);
        }

        // qty on hand
        $qtyOnHandSelect = clone $this->getSelect();
        $qtyOnHandSelect->reset();
        $reportManagement = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $qtyOnHandSelect->from(['w' => $this->getTable('inventory_source_item')]);
            $qtyOnHandSelect->group('w.sku');
            $qtyOnHandSelect->columns(
                ['qty_on_hand' => new \Zend_Db_Expr('IFNULL(IFNULL(SUM(w.quantity),0),0)')]
            );
            $this->getSelect()->joinLeft(
                ['warehouse_product' => $qtyOnHandSelect],
                'e.sku = warehouse_product.sku',
                ['qty_on_hand']
            );
        } else {
            $qtyOnHandSelect->from(
                [
                    'w' => $this->getTable(Product::MAIN_TABLE)
                ]
            );
            $qtyOnHandSelect->where(
                'w.' . ProductInterface::WAREHOUSE_ID . '!=' . ProductInterface::DEFAULT_SCOPE_ID
            );
            $qtyOnHandSelect->group('w.product_id');
            $qtyOnHandSelect->columns(
                ['qty_on_hand' => new \Zend_Db_Expr('IFNULL(IFNULL(SUM(w.total_qty),0),0)')]
            );
            $this->getSelect()->joinLeft(
                ['warehouse_product' => $qtyOnHandSelect],
                'e.entity_id = warehouse_product.product_id',
                ['qty_on_hand']
            );
        }

        // purchase order item
        $this->getSelect()->joinInner(
            ['po_item' => $this->getTable(Item::TABLE_PURCHASE_ORDER_ITEM)],
            self::MAIN_TABLE_ALIAS . '.entity_id = po_item.product_id ',
            '*'
        );

        // incoming stock
        $incomingStockSelect = clone $this->getSelect();
        $incomingStockSelect->reset();
        $incomingStockSelect->from(['incoming' => $this->getTable(Item::TABLE_PURCHASE_ORDER_ITEM)]);
        $incomingStockSelect->columns(
            [
                'incoming_stock' => new \Zend_Db_Expr(
                    'incoming.qty_orderred - incoming.qty_returned' .' - incoming.qty_transferred'
                ),
                'total_cost' => new \Zend_Db_Expr(
                    '(incoming.qty_orderred - incoming.qty_returned'
                    .' - incoming.qty_transferred) * incoming.cost'
                )
            ]
        );
        $this->getSelect()->join(
            ['at_incoming_stock' => $incomingStockSelect],
            'po_item.purchase_order_item_id = at_incoming_stock.purchase_order_item_id ',
            '*'
        );

        // overdue incoming stock
        $overdueIncomingStockSelect = clone $this->getSelect();
        $overdueIncomingStockSelect->reset();
        $overdueIncomingStockSelect->from(
            ['overdue_incoming' => $this->getTable(Item::TABLE_PURCHASE_ORDER_ITEM)]
        );
        $overdueIncomingStockSelect->columns(
            [
                'overdue_incoming_stock' => new \Zend_Db_Expr(
                    'overdue_incoming.qty_orderred - overdue_incoming.qty_returned'
                    .' - overdue_incoming.qty_transferred'
                )
            ]
        );
        $overdueIncomingStockSelect->join(
            ['expected_po' => $this->getTable(PurchaseOrder::TABLE_PURCHASE_ORDER)],
            'expected_po.purchase_order_id = overdue_incoming.purchase_order_id ',
            ['expected_at']
        );
        $overdueIncomingStockSelect->where(
            'expected_po.expected_at < \'' . (new \DateTime())->format('Y-m-d')
            . '\' and expected_po.expected_at is not null'
        );
        $this->getSelect()->joinLeft(
            ['at_overdue_incoming_stock' => $overdueIncomingStockSelect],
            'po_item.purchase_order_item_id = at_overdue_incoming_stock.purchase_order_item_id ',
            '*'
        );

        // purchase order
        $this->getSelect()->join(
            ['po' => $this->getTable(PurchaseOrder::TABLE_PURCHASE_ORDER)],
            'po_item.purchase_order_id = po.purchase_order_id AND po.status in ('
            . \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status::STATUS_PROCESSING .
            ')',
            '*'
        );

        // supplier
        $this->getSelect()->join(
            ['at_supplier' => $this->getTable('os_supplier')],
            'po.supplier_id = at_supplier.supplier_id',
            '*'
        );

        $this->getSelect()->columns(
            [
                'product_name' => new \Zend_Db_Expr($this->getMappingField('product_name')),
                'supplier' => new \Zend_Db_Expr($this->getMappingField('supplier')),
                'purchase_order' => new \Zend_Db_Expr($this->getMappingField('purchase_order')),
                'qty_on_hand' => new \Zend_Db_Expr($this->getMappingField('qty_on_hand')),
                'incoming_stock' => new \Zend_Db_Expr($this->getMappingField('incoming_stock')),
                'overdue_incoming_stock' => new \Zend_Db_Expr($this->getMappingField('overdue_incoming_stock')),
                'total_cost' => new \Zend_Db_Expr($this->getMappingField('total_cost'))
            ]
        );

        if ($this->isEnabledBarcode && $this->barcode != 'sku') {
            $this->getSelect()->columns(
                [
                    'barcode' => new \Zend_Db_Expr($this->getMappingField('barcode'))
                ]
            );
        }

        $this->getSelect()->group('po_item.product_id');

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
            return $this->mappingField;
        }

        return $this->mappingField[$key];
    }

    /**
     * Set mapping field
     *
     * @param array $key
     * @param array $value
     * @return mixed
     */
    public function setMappingField($key, $value)
    {
        return $this->mappingField[$key] = $value;
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
        foreach ($this->mappingFilterField as $key => $value) {
            if ($field == $key) {
                $field = $value;
                return $this->addFieldToFilterCallBack($field, $condition);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add field to filter callback
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

    /**
     * Filters products into a collection
     *
     * @param int $supplierId
     * @return $this
     */
    public function addSupplierToFilter($supplierId)
    {
        $this->getSelect()->where(
            $this->getConnection()->prepareSqlCondition(
                'at_supplier.supplier_id',
                ['eq' => $supplierId]
            )
        );
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
     * Get select count sql
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
}
