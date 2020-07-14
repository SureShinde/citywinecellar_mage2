<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Grid;

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
     *
     */
    const MAPPING_FIELDS = [
        'shipping_name'=> 'order.shipping_name',
        'shipping_email'=> 'order.customer_email'
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
        $mainTable = 'os_dropship_request',
        $resourceModel = 'Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest'
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['order' => $this->getTable('sales_order_grid')],
                'main_table.order_id = order.entity_id',
                []
            )->columns(
                [
                    'shipping_name' => 'order.shipping_name',
                    'shipping_email' => 'order.customer_email',
                ]
            );
            //->group('main_table.supplier_id');
        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if($field == 'created_at'){
            $field = 'main_table.created_at';
        }
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
}
