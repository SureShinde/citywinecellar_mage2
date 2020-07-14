<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\Grid;

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

    const MAPPING_FIELDS = [
        'supplier_code'=> 'supplier.supplier_code',
        'supplier_name'=> 'supplier.supplier_name',
        'supplier_id'=> 'supplier.supplier_id'
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
        $mainTable = 'os_supplier_pricinglist',
        $resourceModel = 'Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList'
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['supplier' => $this->getTable('os_supplier')],
                'main_table.supplier_id = supplier.supplier_id',
                []
            )->columns(
                [
                    'supplier_code' => new \Zend_Db_Expr(self::MAPPING_FIELDS['supplier_code']),
                    'supplier_name' => new \Zend_Db_Expr(self::MAPPING_FIELDS['supplier_name']),
                    'supplier_id' => new \Zend_Db_Expr(self::MAPPING_FIELDS['supplier_id']),
                ]
            );
        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        foreach (self::MAPPING_FIELDS as $alias => $column) {
            if($field == $alias){
                $field = new \Zend_Db_Expr($column);
            }
        }
        return parent::addFieldToFilter($field, $condition);
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
            if($field == $alias){
                $field = new \Zend_Db_Expr($column);
            }
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * @param $field
     * @param $direction
     * @return mixed
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if($field == 'supplier_id')
            $field = 'main_table.supplier_id';

        return parent::addOrder($field, $direction);

    }
}
