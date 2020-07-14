<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Grid;

use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class ProductDataProvider
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @inheritdoc
     */
    protected $document = Document::class;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    const MAPPING_FIELDS = [
        'total_sku' => 'COUNT(DISTINCT(supplier_product.supplier_product_id))',
        'last_purchase_order_on' => 'MAX(purchase_order.purchased_at)',
        'status' => 'main_table.status',
        'purchase_order_value' => 'SUM(DISTINCT(IFNULL(purchase_order.grand_total_incl_tax,0)))',
    ];

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\Module\Manager $moduleManager,
        $mainTable = 'os_supplier',
        $resourceModel = 'Magestore\SupplierSuccess\Model\ResourceModel\Supplier'
    )
    {
        $this->moduleManager = $moduleManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['supplier_product' => $this->getTable('os_supplier_product')],
                'main_table.supplier_id = supplier_product.supplier_id',
                []
            )
            ->columns(
                [
                    'total_sku' => new \Zend_Db_Expr(self::MAPPING_FIELDS['total_sku']),
                ]
            )
            ->group('main_table.supplier_id');
        if ($this->moduleManager->isOutputEnabled('Magestore_PurchaseOrderSuccess')) {
            $this->getSelect()
                ->joinLeft(
                    ['purchase_order' => $this->getTable('os_purchase_order')],
                    'main_table.supplier_id = purchase_order.supplier_id',
                    []
                )->columns(
                    [
                        'last_purchase_order_on' => new \Zend_Db_Expr(self::MAPPING_FIELDS['last_purchase_order_on']),
                        'purchase_order_value' => new \Zend_Db_Expr(self::MAPPING_FIELDS['purchase_order_value']),
                    ]
                );
        }
        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        foreach (self::MAPPING_FIELDS as $alias => $column) {
            if ($field == $alias) {
                $field = new \Zend_Db_Expr($column);
                if($alias == 'last_purchase_order_on') {
                    $resultCondition = $this->_translateCondition($field, $condition);
                    $this->_select->having($resultCondition, null, \Magento\Framework\DB\Select::TYPE_CONDITION);
                    return $this;
                }
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if ($this->_totalRecords === null) {
            $sql = $this->getSelectCountSql();
            $sql->group('main_table.supplier_id');
            $this->_totalRecords = $this->getConnection()->fetchAll($sql, $this->_bindParams);
        }
        return count($this->_totalRecords);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        foreach (self::MAPPING_FIELDS as $alias => $column) {
            if ($field == $alias) {
                $field = new \Zend_Db_Expr($column);
            }
        }
        return parent::setOrder($field, $direction);
    }
    /**
     * Retrieve all ids for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('main_table.supplier_id');
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
    /**
     * Retrieve clear select
     *
     * @return \Magento\Framework\DB\Select
     */
    public function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    public function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);

        return $select;
    }
}
