<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;

/**
 * Class Collection
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

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
        \Magento\Framework\App\RequestInterface $request,
        $mainTable = 'os_return_order_item_transferred',
        $resourceModel = 'Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Transferred'
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $id = $this->request->getParam('return_id', null);
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['item' => $this->getTable('os_return_order_item')],
                'main_table.return_item_id = item.return_item_id',
                ['product_sku', 'product_name', 'product_supplier_sku']
            );
//            ->joinLeft(
//                ['return' => $this->getTable('os_return_order')],
//                'item.return_id = return.return_id',
//                ['warehouse_id']
//            );
        if($id)
            $this->getSelect()->where('item.return_id = ?', $id);
        return $this;
    }

    /**
     * Add field filters to collection
     *
     * @see self::_getConditionSql for $condition
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if($field == 'qty_transferred')
            $field = 'main_table.qty_transferred';
        return parent::addFieldToFilter($field, $condition);
    }
}