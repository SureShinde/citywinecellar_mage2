<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\ResourceModel\Report\StockDetails;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder;
use Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Product;
use Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface;
use Magento\Framework\Module\Manager;

/**
 * Class \Magestore\ReportSuccess\Model\ResourceModel\Report\StockDetails\Collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @var array
     */
    protected $mappingField = [
        'qty_on_hand' => 'warehouse_product.qty_on_hand',
        'available_qty' => 'warehouse_product.available_qty',
        'qty_to_ship' => 'warehouse_product.qty_to_ship',
        'incoming_qty' => 'at_incoming_qty.incoming_qty',
        'warehouse' => 'warehouse_product.location_id',
        'supplier' => 'supplier_select.supplier'
    ];

    /**
     * @var array
     */
    protected $mappingFilterField = [
        'qty_on_hand' => 'warehouse_product.qty_on_hand',
        'available_qty' => 'warehouse_product.available_qty',
        'qty_to_ship' => 'warehouse_product.qty_to_ship',
        'incoming_qty' => 'at_incoming_qty.incoming_qty',
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

        // warehouse qty
        $this->warehouseJoinData();

        if ($objectManager->get(Manager::class)->isEnabled('Magestore_SupplierSuccess')) {
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

        if ($objectManager->get(Manager::class)->isEnabled('Magestore_PurchaseOrderSuccess')) {
            // purchase order item
            $this->getSelect()->joinLeft(
                ['po_item' => $this->getTable(Item::TABLE_PURCHASE_ORDER_ITEM)],
                self::MAIN_TABLE_ALIAS . '.entity_id = po_item.product_id ',
                ['purchase_order_item_id']
            );
            // incoming stock
            $incomingStockSelect = clone $this->getSelect();
            $incomingStockSelect->reset();
            $incomingStockSelect->from(['incoming' => $this->getTable(Item::TABLE_PURCHASE_ORDER_ITEM)]);
            $incomingStockSelect->joinLeft(
                ['po' => $this->getTable(PurchaseOrder::TABLE_PURCHASE_ORDER)],
                'incoming.purchase_order_id = po.purchase_order_id',
                ['status']
            );
            $incomingStockSelect->where(
                'po.status in ('
                . \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status::STATUS_PROCESSING
                . ')'
            );
            $incomingStockSelect->columns(
                [
                    'incoming_qty' => new \Zend_Db_Expr(
                        'SUM(incoming.qty_orderred - incoming.qty_returned'
                        . ' - incoming.qty_transferred)'
                    )
                ]
            );
            $incomingStockSelect->group('incoming.product_id');

            // fix bug filter by location
            $tmpIncomingStock = clone $this->getSelect();
            $tmpIncomingStock->reset();
            $tmpIncomingStock->from(['tmp_incoming' => $this->getTable(Item::TABLE_PURCHASE_ORDER_ITEM)]);
            $tmpIncomingStock->joinLeft(
                ['in' => $incomingStockSelect],
                'in.product_id = tmp_incoming.product_id',
                ['incoming_qty']
            );

            $this->getSelect()->joinLeft(
                ['at_incoming_qty' => $tmpIncomingStock],
                'po_item.purchase_order_item_id = at_incoming_qty.purchase_order_item_id ',
                []
            );

            $this->getSelect()->columns(
                ['incoming_qty' => new \Zend_Db_Expr($this->getMappingField('incoming_qty'))]
            );
        }

        $this->getSelect()->columns(
            [
                'qty_on_hand' => new \Zend_Db_Expr($this->getMappingField('qty_on_hand')),
                'available_qty' => new \Zend_Db_Expr($this->getMappingField('available_qty')),
                'qty_to_ship' => new \Zend_Db_Expr($this->getMappingField('qty_to_ship')),
                'warehouse' => new \Zend_Db_Expr($this->getMappingField('warehouse'))
            ]
        );

        if ($this->isEnabledBarcode && $this->barcode != 'sku') {
            $this->getSelect()->columns(
                ['barcode' => new \Zend_Db_Expr($this->getMappingField('barcode'))]
            );
        }

        $reportManagement = $objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $this->getSelect()->group('warehouse_product.sku');
        } else {
            $this->getSelect()->group('warehouse_product.product_id');
        }

        return $this;
    }

    /**
     * Warehouse join data
     *
     * @param int|null $warehouseId
     * @return $this
     */
    public function warehouseJoinData($warehouseId = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reportManagement = $objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $qtyToShipSql = $this->createQtyToShipTempTable();
            $qtyOnHandSelect = clone $this->getSelect();
            $qtyOnHandSelect->reset();
            $qtyOnHandSelect->from(['w' => $this->getTable('inventory_source_item')]);
            $qtyOnHandSelect->joinLeft(
                ['shipTable' => $qtyToShipSql],
                'w.sku = shipTable.sku and w.source_code = shipTable.source_code',
                ''
            );
            $qtyOnHandSelect->group('w.sku');
            $qtyOnHandSelect->columns(
                ['qty_on_hand' => new \Zend_Db_Expr('IFNULL(IFNULL(SUM(w.quantity),0),0)')]
            );
            $qtyOnHandSelect->columns(
                [
                    'available_qty' => new \Zend_Db_Expr(
                        'SUM( IFNULL(w.quantity,0)'
                        . ' - IFNULL(shipTable.qty_to_ship,0))'
                    )
                ]
            );
            $qtyOnHandSelect->columns(
                ['qty_to_ship' => new \Zend_Db_Expr('IFNULL(IFNULL(SUM(shipTable.qty_to_ship),0),0)')]
            );
            $qtyOnHandSelect->columns(
                [
                    'location_id' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT w.source_code)')
                ]
            );
            if ($warehouseId) {
                $qtyOnHandSelect->where(
                    $this->getConnection()->prepareSqlCondition('w.source_code', ['in' => $warehouseId])
                );
            }
            $this->getSelect()->join(
                ['warehouse_product' => $qtyOnHandSelect],
                'e.sku = warehouse_product.sku',
                []
            );
        } else {
            // warehouse qty
            $qtyOnHandSelect = clone $this->getSelect();
            $qtyOnHandSelect->reset();
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
            $qtyOnHandSelect->columns(
                ['available_qty' => new \Zend_Db_Expr('IFNULL(IFNULL(SUM(w.qty),0),0)')]
            );
            $qtyOnHandSelect->columns(
                ['qty_to_ship' => new \Zend_Db_Expr('IFNULL(IFNULL(SUM(w.total_qty - w.qty),0),0)')]
            );
            $qtyOnHandSelect->columns(
                [
                    'location_id' => new \Zend_Db_Expr(
                        'GROUP_CONCAT(DISTINCT w.' .
                        ProductInterface::WAREHOUSE_ID . ')'
                    )
                ]
            );
            if ($warehouseId) {
                $qtyOnHandSelect->where(
                    $this->getConnection()->prepareSqlCondition(
                        'w.' . ProductInterface::WAREHOUSE_ID,
                        ['in' => $warehouseId]
                    )
                );
            }
            $this->getSelect()->join(
                ['warehouse_product' => $qtyOnHandSelect],
                'e.entity_id = warehouse_product.product_id',
                []
            );
        }
        return $this;
    }

    /**
     * Get pick item sql
     *
     * @return mixed
     */
    public function getPickItemsSql()
    {
        /* pick items sql */
        $pickRequestItemQty = clone $this->getSelect();
        $pickRequestItemQty->reset();
        $pickRequestItemQty->from(
            [
                'pickRequestItems' => $this->getTable('os_fulfilsuccess_pickrequest_item')
            ],
            ['pick_request_item_id', 'product_id']
        );
        $pickRequestItemQty->join(
            ['pickRequest' => $this->getTable('os_fulfilsuccess_pickrequest')],
            ' pickRequestItems.pick_request_id = pickRequest.pick_request_id',
            ['source_code']
        );
        $pickRequestItemQty->columns(
            ['request_qty' => new \Zend_Db_Expr('SUM(pickRequestItems.request_qty)')]
        );
        $pickRequestItemQty->where(
            $this->getConnection()->prepareSqlCondition(
                'pickRequest.' . \Magestore\FulfilSuccess\Api\Data\PickRequestInterface::STATUS,
                ['in' => \Magestore\FulfilSuccess\Api\Data\PickRequestInterface::STATUS_PICKING]
            )
        );
        $pickRequestItemQty->group('pickRequestItems.product_id');
        $pickRequestItemQty->group('pickRequest.source_code');
        return $pickRequestItemQty;
    }

    /**
     * Get pack items sql
     *
     * @return mixed
     */
    public function getPackItemsSql()
    {
        $packRequestItemQty = clone $this->getSelect();
        $packRequestItemQty->reset();
        $packRequestItemQty->from(
            [
                'packRequestItems' => $this->getTable('os_fulfilsuccess_packrequest_item')
            ],
            ['pack_request_item_id', 'product_id']
        );
        $packRequestItemQty->join(
            ['packRequest' => $this->getTable('os_fulfilsuccess_packrequest')],
            ' packRequestItems.pack_request_id = packRequest.pack_request_id',
            ['source_code']
        );
        $packRequestItemQty->columns(
            [
                'packing_qty' => new \Zend_Db_Expr(
                    'SUM( IFNULL(packRequestItems.request_qty,0)'
                    . ' - IFNULL(packRequestItems.packed_qty,0) )'
                )
            ]
        );
        $packRequestItemQty->where(
            $this->getConnection()->prepareSqlCondition(
                'packRequest.' . \Magestore\FulfilSuccess\Api\Data\PackRequestInterface::STATUS,
                [
                    'in' => [
                        \Magestore\FulfilSuccess\Api\Data\PackRequestInterface::STATUS_PACKING,
                        \Magestore\FulfilSuccess\Api\Data\PackRequestInterface::STATUS_PARTIAL_PACK
                    ]
                ]
            )
        );
        $packRequestItemQty->group('packRequestItems.product_id');
        $packRequestItemQty->group('packRequest.source_code');
        return $packRequestItemQty;
    }

    /**
     * Catalog items sql
     *
     * @return mixed
     */
    public function catalogItemsSql()
    {
        $catalogItemsSql = clone $this->getSelect();
        $catalogItemsSql->reset();
        $catalogItemsSql->from(
            [
                'source_item' => $this->getTable('inventory_source_item')
            ],
            ['source_code', 'sku']
        );
        $catalogItemsSql->join(
            ['catalog_item' => $this->getTable('catalog_product_entity')],
            ' source_item.sku = catalog_item.sku',
            ['entity_id']
        );
        return $catalogItemsSql;
    }

    /**
     * Create qty to ship temp table
     *
     * @return mixed
     */
    public function createQtyToShipTempTable()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reportManagement = $objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if (!$reportManagement->isFulFilSuccessEnable()) {
            $qtyToShip = clone $this->getSelect();
            $qtyToShip->reset();
            $qtyToShip->from(['catalog' => $this->getTable('catalog_product_entity')], 'sku');
            $qtyToShip->columns(['product_id' => new \Zend_Db_Expr('catalog.entity_id')]);
            $qtyToShip->columns(['source_code' => new \Zend_Db_Expr('0')]);
            $qtyToShip->columns(['qty_to_ship' => new \Zend_Db_Expr('0')]);
            $qtyToShip->where('catalog.entity_id = 0');
            return $qtyToShip;
        }

        /* pick items sql */
        $pickRequestItemQty = $this->getPickItemsSql();

        /* pack items sql */
        $packRequestItemQty = $this->getPackItemsSql();

        /* catalog items sql */
        $catalogItemsSql = $this->catalogItemsSql();

        /* qty_to_ship sql */
        $qtyToShip = clone $this->getSelect();
        $qtyToShip->reset();
        $qtyToShip->from(['catalog' => $catalogItemsSql], 'sku');

        $qtyToShip->joinLeft(
            ['tempPick' => $pickRequestItemQty],
            'catalog.entity_id = tempPick.product_id and catalog.source_code = tempPick.source_code',
            ''
        );
        $qtyToShip->joinLeft(
            ['tempPack' => $packRequestItemQty],
            'catalog.entity_id = tempPack.product_id and catalog.source_code = tempPack.source_code',
            ''
        );
        $qtyToShip->columns(['product_id' => new \Zend_Db_Expr('catalog.entity_id')]);
        $qtyToShip->columns(
            ['source_code' => new \Zend_Db_Expr('IFNULL(tempPick.source_code, tempPack.source_code)')]
        );
        $qtyToShip->columns(
            [
                'qty_to_ship' => new \Zend_Db_Expr(
                    'SUM( IFNULL(tempPick.request_qty,0)'
                    . ' + IFNULL(tempPack.packing_qty,0))'
                )
            ]
        );
        $qtyToShip->where('IFNULL(tempPick.request_qty, IFNULL(tempPack.packing_qty,0)) != 0');
        $qtyToShip->group('catalog.sku');
        $qtyToShip->group('source_code');

        return $qtyToShip;
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
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function setMappingField($key, $value)
    {
        return $this->mappingField[$key] = $value;
    }

    /**
     * Add warehouse to filter
     *
     * @param int $warehouseId
     * @return $this
     * @throws \Zend_Db_Select_Exception
     */
    public function addWarehouseToFilter($warehouseId)
    {
        $from = $this->getSelect()->getPart(\Zend_Db_Select::FROM);
        if (isset($from['warehouse_product'])) {
            unset($from['warehouse_product']);
        }
        $this->getSelect()->setPart(\Zend_Db_Select::FROM, $from);
        $this->warehouseJoinData($warehouseId);

        return $this;
    }

    /**
     * Filters products into a collection
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
     * Add field to filter
     *
     * @param mixed $field
     * @param array|null $condition
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
