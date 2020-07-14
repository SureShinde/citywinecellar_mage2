<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\ResourceModel\Report\StockByLocation;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation\Metric;
use Magestore\InventorySuccess\Model\ResourceModel\Warehouse\Product;

/**
 * Class \Magestore\ReportSuccess\Model\ResourceModel\Report\StockByLocation\Collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @var array
     */
    protected $mappingField = [];

    /**
     * @var array
     */
    protected $mappingWithoutMacField = [];

    /**
     * @var bool
     */
    protected $isEnabledBarcode = false;

    /**
     * @var string
     */
    protected $barcode = '';

    /**
     * @var array
     */
    protected $metrics = [
        Metric::QTY_ON_HAND => 's.total_qty',
        Metric::AVAILABLE_QTY => 's.qty',
        Metric::QTY_TO_SHIP => 's.total_qty - s.qty',
    ];

    /**
     * @var string
     */
    protected $metric = Metric::QTY_ON_HAND;

    /**
     * Construct
     */
    protected function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reportManagement = $objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        if ($reportManagement->isMSIEnable()) {
            $this->metrics[Metric::QTY_ON_HAND] = 'IFNULL((s.quantity),0)';
            $this->metrics[Metric::AVAILABLE_QTY] = '( IFNULL(s.quantity,0) - IFNULL(shipTable.qty_to_ship,0))';
            $this->metrics[Metric::QTY_TO_SHIP] = 'IFNULL((shipTable.qty_to_ship),0)';
        }

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
     * Calculate website scope config
     *
     * @param int $catalogPriceScope
     */
    public function calculateWebsiteScopeConfig($catalogPriceScope)
    {
        $qtyOnHandSql = '`location`.`total_qty`';
        if ($catalogPriceScope) {
            $priceSql = 'IF(at_price.value_id > 0, at_price.value, at_price_default.value)';
            $macSql = 'IFNULL(IF(at_mac.value_id > 0, at_mac.value, at_mac_default.value),IF(IF(at_cost.value_id > 0,'
                . ' at_cost.value, at_cost_default.value) is not null,IF(at_cost.value_id > 0,'
                . ' at_cost.value, at_cost_default.value),null))';
            $macWithoutPurchaseSql = 'IF(at_cost.value_id > 0, at_cost.value, at_cost_default.value)';
        } else {
            $priceSql = 'IFNULL(`at_price`.`value`,0)';
            $macSql = 'IFNULL(`at_mac`.`value`,IF(`at_cost`.`value` is not null,at_cost.value,null))';
            $macWithoutPurchaseSql = '`at_cost`.`value`';
        }

        $this->mappingField[Metric::PROFIT_VALUE] = $priceSql . '*' . $qtyOnHandSql . '-' . $macSql . '*'
            . $qtyOnHandSql;
        $this->mappingField[Metric::INVENTORY_VALUE] = $macSql . '*' . $qtyOnHandSql;
        $this->mappingField[Metric::POTENTIAL_REVENUE] = $priceSql . '*' . $qtyOnHandSql;

        $this->mappingWithoutMacField[Metric::PROFIT_VALUE] = $priceSql . '*' . $qtyOnHandSql . '-'
            . $macWithoutPurchaseSql . '*' . $qtyOnHandSql;
        $this->mappingWithoutMacField[Metric::INVENTORY_VALUE] = $macWithoutPurchaseSql . '*' . $qtyOnHandSql;
        $this->mappingWithoutMacField[Metric::POTENTIAL_REVENUE] = $priceSql . '*' . $qtyOnHandSql;
    }

    /**
     * Init select
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
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
        $this->addAttributeToSelect('name');
        $this->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $this->addAttributeToFilter(
            \Magento\Catalog\Api\Data\ProductInterface::STATUS,
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        );

        if ($this->isEnabledBarcode && $this->barcode != 'sku') {
            $this->addExpressionAttributeToSelect('barcode', '', $this->barcode);
        }

        if ($this->isEnabledBarcode && $this->barcode != 'sku') {
            $this->getSelect()->columns(
                [
                    'barcode' => new \Zend_Db_Expr($this->getMappingField('barcode'))
                ]
            );
        }
        return $this;
    }

    /**
     * Join location data
     *
     * @param array $locations
     * @param string $metric
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function joinLocationsData($locations, $metric)
    {
        // Update metric
        $this->metric = $metric;

        // SQL
        $select = clone $this->getSelect();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reportManagement = $objectManager->get(\Magestore\ReportSuccess\Api\ReportManagementInterface::class);
        $isMSIEnable = $reportManagement->isMSIEnable();

        if ($isMSIEnable) {
            $select->reset()->from(
                ['s' => $this->getTable('inventory_source_item')],
                ['sku']
            );
            $qtyToShipSql = $objectManager
                ->get(\Magestore\ReportSuccess\Model\ResourceModel\Report\StockDetails\Collection::class)
                ->createQtyToShipTempTable();
            $select->joinLeft(
                ['shipTable' => $qtyToShipSql],
                's.sku = shipTable.sku and s.source_code = shipTable.source_code',
                ''
            );
            $select->group('s.sku');

        } else {
            $select->reset()->from(
                ['s' => $this->getTable(Product::MAIN_TABLE)],
                ['product_id']
            );
            $select->group('s.product_id');
        }

        // Add data for each location
        $sum = isset($this->metrics[$metric]) ? $this->metrics[$metric] : 's.quantity';

        foreach ($locations as $location) {
            if (' ' == $location) {
                $select->columns(
                    [
                        'loc__' => new \Zend_Db_Expr('SUM(' . $sum . ')'),
                    ]
                );
                $isMSIEnable ? "" : $select->where(
                    's.stock_id <> ?',
                    \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID
                );
            } else {
                $isMSIEnable ? $select->columns(
                    [
                        "loc_$location" => new \Zend_Db_Expr(
                            'SUM(IF(s.source_code = "'
                            . $location
                            . '",' . $sum . ',0))'
                        ),
                    ]
                ) : $select->columns(
                    [
                        "loc_$location" => new \Zend_Db_Expr(
                            'SUM(IF(s.stock_id = '
                            . $location
                            . ',' . $sum . ',0))'
                        ),
                    ]
                );
            }
        }

        // Filter products by stock locations
        if (!in_array(' ', $locations)) {
            $isMSIEnable ? $select->where('s.source_code IN (?)', $locations) :
                $select->where('s.stock_id IN (?)', $locations);
        }

        // Join to main table
        if (isset($this->mappingField[$metric])) {
            $columns = [];
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
            foreach ($locations as $location) {
                if (' ' == $location) {
                    $location = '_';
                }
                $columns["loc_$location"] = new \Zend_Db_Expr(
                    str_replace('total_qty', "loc_$location", $this->getMappingField($metric))
                );
            }
        } else {
            $columns = '*';
        }

        $this->getSelect()->joinInner(
            ['location' => $select],
            $isMSIEnable ? 'e.sku = location.sku' : 'e.entity_id = location.product_id',
            $columns
        );
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
     * Add order
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if (0 === strpos($field, 'loc_')) {
            if (isset($this->metrics[$this->metric])) {
                // Sort by metric
                $this->getSelect()->order(new \Zend_Db_Expr('location.' . $field . ' ' . $direction));
                return $this;
            }
            $field = str_replace('total_qty', $field, $this->getMappingField($this->metric));
            $this->getSelect()->order(new \Zend_Db_Expr($field . ' ' . $direction));
            return $this;
        }
        return parent::addOrder($field, $direction);
    }
}
