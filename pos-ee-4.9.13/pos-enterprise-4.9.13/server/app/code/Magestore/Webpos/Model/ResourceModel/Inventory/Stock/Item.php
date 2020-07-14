<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Inventory\Stock;

use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

/**
 * Stock item
 *
 * Class \Magestore\Webpos\Model\ResourceModel\Inventory\Stock\Item
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Item extends \Magento\CatalogInventory\Model\ResourceModel\Stock\Item
{
    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $date;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * Item constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Processor $processor
     * @param StockConfigurationInterface $stockConfigurationInterface
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param \Magento\Framework\Stdlib\DateTime $date
     * @param \Magento\Framework\Registry $registry
     * @param string|null $connectionName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Processor $processor,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfigurationInterface,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Framework\Stdlib\DateTime $date,
        \Magento\Framework\Registry $registry,
        $connectionName = null
    ) {
        parent::__construct($context, $processor, $connectionName);
        $this->stockConfiguration = $stockConfigurationInterface;
        $this->productMetadata = $productMetadata;
        $this->countryFactory = $countryFactory;
        $this->eavConfig = $eavConfig;
        $this->webposManagement = $webposManagement;
        $this->stockManagement = $stockManagement;
        $this->date = $date;
        $this->registry = $registry;
        $this->dateTime = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
    }

    /**
     * Add stock data to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    public function addStockDataToCollection($collection)
    {
        $collection = $this->joinStockItemTable($collection);
        $productEntityId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
        $collection->getSelect()->join(
            ['ea' => $this->getTable('eav_attribute')],
            "ea.entity_type_id = $productEntityId AND ea.attribute_code = 'name'",
            [
                'name_attribute_id' => 'attribute_id'
            ]
        );

        if (!$this->isMagentoEnterprise()) {
            $collection->getSelect()->join(
                ['cpev' => $this->getTable('catalog_product_entity_varchar')],
                "cpev.entity_id = e.entity_id AND cpev.attribute_id = ea.attribute_id",
                [
                    'name' => 'value'
                ]
            );
        } else {
            $collection->getSelect()->join(
                ['cpev' => $this->getTable('catalog_product_entity_varchar')],
                "cpev.row_id = e.row_id AND cpev.attribute_id = ea.attribute_id",
                [
                    'name' => 'value'
                ]
            );
        }

        $this->filterByStockAndSource($collection);

        return $collection;
    }

    /**
     * Join Stock Item table
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    public function joinStockItemTable($collection)
    {
        $websiteId = $this->stockConfiguration->getDefaultScopeId();

        $joinCondition = $this->getConnection()->quoteInto(
            'e.entity_id = stock_item_index.product_id',
            $websiteId
        );

        $joinFields = [
            'item_id' => 'item_id',
            'stock_id' => 'stock_id',
            'product_id' => 'product_id',
            'qty' => 'qty',
            'manage_stock' => 'manage_stock',
            'use_config_manage_stock' => 'use_config_manage_stock',
            'backorders' => 'backorders',
            'use_config_backorders' => 'use_config_backorders',
            'min_qty' => 'min_qty',
            'use_config_min_qty' => 'use_config_min_qty',
            'min_sale_qty' => 'min_sale_qty',
            'use_config_min_sale_qty' => 'use_config_min_sale_qty',
            'max_sale_qty' => 'max_sale_qty',
            'use_config_max_sale_qty' => 'use_config_max_sale_qty',
            'is_qty_decimal' => 'is_qty_decimal',
            'use_config_qty_increments' => 'use_config_qty_increments',
            'qty_increments' => 'qty_increments',
            'use_config_enable_qty_inc' => 'use_config_enable_qty_inc',
            'enable_qty_increments' => 'enable_qty_increments',
//                'updated_time' => 'updated_time',
        ];

        if (!$this->webposManagement->isMSIEnable()) {
            $joinFields['is_in_stock'] = 'is_in_stock';
        }

        $collection->getSelect()->join(
            ['stock_item_index' => $this->getMainTable()],
            $joinCondition,
            $joinFields
        );
        return $collection;
    }

    /**
     * Filter by stock and source
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function filterByStockAndSource($collection)
    {
        $stockId = $this->stockManagement->getStockId();
        if (!$stockId) {
            return $collection;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
        $stockTable = $objectManager
            ->get(\Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface::class)
            ->execute($stockId);
        if (!$resource->getConnection()->isTableExists($stockTable)) {
            return $collection;
        }
        $sourceItemTable = $resource->getTableName('inventory_source_item');
        $linkedSources = $this->stockManagement->getLinkedSourceCodesByStockId($stockId);
        $reservationTable = $resource->getTableName('inventory_reservation');
        $select = $resource->getConnection()->select()
            ->from(['main_table' => $reservationTable], ['sku'])
            ->where('stock_id = ?', $stockId)
            ->columns(['quantity' => 'SUM(IF(main_table.quantity, main_table.quantity, 0))'])
            ->group('sku');
        $collection->getSelect()
            ->joinLeft(
                ['inventory_source_item' => $sourceItemTable],
                "e.sku = inventory_source_item.sku 
                    AND inventory_source_item.source_code IN ('" . implode("', '", $linkedSources) . "')",
                ['source_code', 'quantity']
            )->joinLeft(
                ['stock_table' => $stockTable],
                'e.sku = stock_table.sku',
                ['is_salable']
            )->joinLeft(
                ['reservation' => $select],
                "e.sku = reservation.sku",
                [
                    'qty' => '(IF(stock_table.quantity, stock_table.quantity, 0)'
                        .' + IF(reservation.quantity, reservation.quantity, 0))'
                ]
            )->having('inventory_source_item.source_code IN (?)', $linkedSources)
            ->orHaving('stock_table.is_salable = ?', 1);

        $collection->getSelect()->columns(
            ['is_in_stock' => 'IF(stock_table.is_salable, stock_table.is_salable, 0)']
        );
    }

    /**
     * Is Magento EE
     *
     * @return bool
     */
    public function isMagentoEnterprise()
    {
        $edition = $this->productMetadata->getEdition();
        return $edition == 'Enterprise' || $edition == 'B2B';
    }

    /**
     * Get available qty
     *
     * @param int $product_id
     * @param int $website_id
     * @return array
     */
    public function getAvailableQty($product_id, $website_id = 0)
    {
        $connection = $this->getConnection();

        $select = $connection->select();
        $select->from(['e' => $this->getTable('cataloginventory_stock_item')]);
        $select->where('product_id = ' . $product_id);
        $select->where('website_id = ' . $website_id);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->columns("qty");

        $qtys = $connection->fetchAll($select);

        return $qtys;
    }

    /**
     * Get external stock
     *
     * @param int $product_id
     * @param int $location_id
     * @return array
     */
    public function getExternalStock($product_id, $location_id)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(['e' => $this->getTable('cataloginventory_stock_item')]);
        $select->where('product_id = ' . $product_id);
        $select->where('website_id != 0');
//        $select->where('website_id != '.$location_id);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $isCurrentLocationSql = new \Zend_Db_Expr('IF(location.warehouse_id = ' . $location_id . ', 1,0)');
        $select->joinLeft(
            ['location' => $this->getTable('os_warehouse')],
            'e.website_id = location.warehouse_id',
            [
                'name' => 'location.warehouse_name',
                'street' => 'location.street',
                'city' => 'location.city',
                'country_id' => 'location.country_id',
                'postcode' => 'location.postcode',
                'is_current_location' => $isCurrentLocationSql,
                'is_in_stock' => 'e.is_in_stock',
                'use_config_manage_stock' => 'e.use_config_manage_stock',
                'manage_stock' => 'e.manage_stock'
            ]
        );
        $select->columns(['qty']);
        $select->order('e.qty DESC');
        $qtys = $connection->fetchAll($select);

        $countryNames = [];
        foreach ($qtys as $key => $_qty) {
            if (!isset($countryNames[$_qty['country_id']])) {
                $countryModel = $this->countryFactory->create()->loadByCode($_qty['country_id']);
                $countryNames[$_qty['country_id']] = $countryModel->getName();
            }
            $qtys[$key]['address'] = $_qty['street'] . ', ' . $_qty['city'] . ', '
                . $countryNames[$_qty['country_id']] . ', ' . $_qty['postcode'];
            $qtys[$key]['qty'] = round($_qty['qty'], 4);
            unset($qtys[$key]['street']);
            unset($qtys[$key]['city']);
            unset($qtys[$key]['country_id']);
            unset($qtys[$key]['postcode']);
        }
        unset($countryNames);
        return $qtys;
    }

    /**
     * Get MSI external stock
     *
     * @param string $sku
     * @param int $location_id
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getMsiExternalStock($sku, $location_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $getAssignedStockIdsBySku = $objectManager
            ->create(\Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku::class);
        $getProductSalableQty = $objectManager
            ->create(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class);
        $getStockItemConfiguration = $objectManager
            ->create(\Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface::class);
        $getStockItem = $objectManager->create(\Magento\InventorySalesApi\Model\GetStockItemDataInterface::class);

        $stockIds = $getAssignedStockIdsBySku->execute($sku);
        if (count($stockIds)) {
            $connection = $this->getConnection();
            $select = $connection->select();
            $select->from(['e' => $this->getTable('webpos_location')]);
            $select->where('e.stock_id in (?)', $stockIds);
            $select->order('e.location_id ASC');
            $locations = $connection->fetchAll($select);
            $countryNames = [];
            $existedStockId = [];
            foreach ($locations as $key => $_qty) {
                if (!isset($countryNames[$_qty['country_id']])) {
                    $countryModel = $this->countryFactory->create()->loadByCode($_qty['country_id']);
                    $countryNames[$_qty['country_id']] = $countryModel->getName();
                }
                if (!isset($existedStockId[$_qty['stock_id']])) {
                    $stockItemConfiguration = $getStockItemConfiguration->execute($sku, $_qty['stock_id']);
                    $getStockItemData = $getStockItem->execute($sku, $_qty['stock_id']);
                    $isInStock = $getStockItemData['is_salable'];
                    $useManageStock = $stockItemConfiguration->isUseConfigManageStock();
                    $isManageStock = $stockItemConfiguration->isManageStock();
                    $minQty = $stockItemConfiguration->getMinQty();
                    $qty = $isManageStock ? $getProductSalableQty->execute($sku, $_qty['stock_id']) : null;
                    $existedStockId[$_qty['stock_id']] = [
                        'is_in_stock' => $isInStock ? "1" : "0",
                        'use_config_manage_stock' => $useManageStock ? "1" : "0",
                        'manage_stock' => $isManageStock ? "1" : "0",
                        'min_qty' => $minQty,
                        'qty' => $qty ? round($qty, 4) : null
                    ];
                }
                $locations[$key]['is_current_location'] = ($_qty['location_id'] == $location_id) ? "1" : "0";
                $locations[$key]['address'] = $_qty['street'] . ', ' . $_qty['city'] . ', '
                    . $countryNames[$_qty['country_id']] . ', ' . $_qty['postcode'];
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                $locations[$key] = array_merge($locations[$key], $existedStockId[$_qty['stock_id']]);
            }
            return $locations;
        }
        return [];
    }

    /**
     * Update time by sku
     *
     * @param array $skus
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateUpdatedTimeBySku($skus)
    {
        if (is_array($skus) && !empty($skus)) {
            $skus = array_unique($skus);
            $processedSkus = $this->registry->registry('webpos_save_stock_item_sku_updated_time');
            if (!empty($processedSkus) && is_array($processedSkus)) {
                $skus = array_diff($skus, $processedSkus);
                $this->registry->unregister('webpos_save_stock_item_sku_updated_time');
                $this->registry->register(
                    'webpos_save_stock_item_sku_updated_time',
                    array_merge($skus, $processedSkus)
                );
            }
            $productTable = $this->getTable('catalog_product_entity');
            $stockItemTable = $this->getMainTable();
            $connection = $this->getConnection();
            $statement = $connection->select()->from(
                $productTable,
                ['entity_id']
            )->where(
                'sku IN (?)',
                $skus
            );
//            $productIds = $connection->fetchCol($statement);
            $connection->update(
                $stockItemTable,
                ['updated_time' => $this->date->formatDate($this->dateTime->gmtTimestamp())],
                ['product_id IN (?)' => new \Zend_Db_Expr($statement->__toString())]
            );
        }
    }
}
