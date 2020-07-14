<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\DataProvider\Location\Inventory;

use Magestore\Webpos\Model\ResourceModel\Location\Inventory\Grid\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class LocationInventoryDataProvider
 * @package Magento\Webpos\Ui\DataProvider\Location\Inventory
 */
class LocationInventoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagementInterface;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var PoolInterface
     */
    private $modifiersPool;

    protected $mappingFields = [
        'quantity' => [
            'table_alias' => 'stock_table',
            'field' => 'quantity',
            'condition' => 'e.sku = stock_table.sku'
        ]
    ];

    /**
     * LocationInventoryDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagementInterface
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagementInterface,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->requestInterface = $requestInterface;
        $this->objectManager = $objectManager;
        $this->stockManagementInterface = $stockManagementInterface;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->modifiersPool = $modifiersPool ?: ObjectManager::getInstance()->get(PoolInterface::class);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $stockId = $this->requestInterface->getParam('stock_id');
        $stock = null;
        $stockTable = "";
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = $this->objectManager->create('Magento\Framework\App\ResourceConnection');
        if ($stockId) {
            /** @var \Magento\InventoryApi\Api\StockRepositoryInterface $stock */
            $stockRepository = $this->objectManager->create('Magento\InventoryApi\Api\StockRepositoryInterface');
            try {
                $stock = $stockRepository->get($stockId);
            } catch (\Exception $e) {
                $stock = null;
            }
            $stockTable = $this->objectManager
                ->get('Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface')
                ->execute($stockId);
        }
        if ($stock && $stock->getStockId() && $stockTable && $resource->getConnection()->isTableExists($stockTable)) {
            $sourceItemTable = $resource->getTableName('inventory_source_item');
            $linkedSources = $this->stockManagementInterface->getLinkedSourceCodesByStockId($stock->getStockId());
            $quantityFieldCondition = $this->getQuantityFieldCondition();
            $this->getCollection()->getSelect()
                ->joinLeft(
                    ['stock_table' => $stockTable],
                    'e.sku = stock_table.sku',
                    [
                        'quantity' => $quantityFieldCondition,
                        'is_salable' => 'is_salable'
                    ]
                )->joinLeft(
                    ['inventory_source_item' => $sourceItemTable],
                    "e.sku = inventory_source_item.sku 
                    AND inventory_source_item.source_code IN ('" . implode("', '", $linkedSources) . "')",
                    ['source_code']
                )->group('e.entity_id')
                ->having('inventory_source_item.source_code IN (?)', $linkedSources)
                ->orHaving('stock_table.is_salable = ?', 1);
        } else {
            $this->getCollection()->addFieldToFilter('entity_id', 0);
        }
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }
        return $data;
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() === 'quantity') {
            $condition = [$filter->getConditionType() => $filter->getValue()];
            $quantityFieldCondition = $this->getQuantityFieldCondition();
            if (isset($condition['gteq'])) {
                $this->getCollection()->getSelect()->where(
                    "$quantityFieldCondition >= ?",
                    (float)$condition['gteq']
                );
            }
            if (isset($condition['lteq'])) {
                $whereQuantityCondition = "($quantityFieldCondition <= ? OR $quantityFieldCondition IS NULL)";
                $whereConditionArray = $this->getCollection()->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
                if (isset($whereConditionArray) && is_array($whereConditionArray)) {
                    foreach ($whereConditionArray as $whereCondition) {
                        $hasGTEQQuantity = strpos($whereCondition, "$quantityFieldCondition >=");
                        if ($hasGTEQQuantity !== false && $hasGTEQQuantity >= 0) {
                            $whereQuantityCondition = "$quantityFieldCondition <= ?";
                        }
                    }
                }
                $this->getCollection()->getSelect()->where($whereQuantityCondition, (float)$condition['lteq']);
            }
        } else {
            if (isset($this->addFilterStrategies[$filter->getField()])) {
                $this->addFilterStrategies[$filter->getField()]
                    ->addFilter(
                        $this->getCollection(),
                        $filter->getField(),
                        [$filter->getConditionType() => $filter->getValue()]
                    );
            } else {
                parent::addFilter($filter);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return void
     */
    public function addOrder($field, $direction)
    {
        if ($field === 'quantity') {
            $stockId = $this->requestInterface->getParam('stock_id');
            if ($stockId) {
                $field = $this->getQuantityFieldCondition();
                $this->getCollection()->getSelect()->order("$field $direction");
//                var_dump($this->getCollection()->getSelect()->__toString());
            }
        } else {
            $this->getCollection()->addOrder($field, $direction);
        }
    }

    /**
     * @return string
     */
    public function getQuantityFieldCondition()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stockConfiguration = $this->objectManager->get('Magento\CatalogInventory\Api\StockConfigurationInterface');
        $manageItemProductType = array_keys(array_filter($stockConfiguration->getIsQtyTypeIds()));
        $manageItemProductTypeSql = is_array($manageItemProductType) ?
            "('" . implode("', '", $manageItemProductType) . "')" :
            "('')";
        return "IF(e.type_id IN $manageItemProductTypeSql, TRIM(stock_table.quantity)+0, NULL)";
    }
}
