<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\PurchaseOrderSuccess\Api\Data\InvoiceItemInterface;

/**
 * Class Collection
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item\Grid
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
        $mainTable = 'os_purchase_order_invoice_item',
        $resourceModel = 'Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Invoice\Item'
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $id = $this->request->getParam('invoice_id', null);
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['purchase_order_item' => $this->getTable('os_purchase_order_item')],
                'main_table.purchase_order_item_id = purchase_order_item.purchase_order_item_id',
                ['product_id', 'product_sku', 'product_name', 'product_supplier_sku']
            )
            ->joinLeft(
                ['purchase_order' => $this->getTable('os_purchase_order')],
                'purchase_order_item.purchase_order_id = purchase_order.purchase_order_id',
                ['currency_code']
            );
        if($id)
            $this->getSelect()->where(
                'main_table.'.InvoiceItemInterface::PURCHASE_ORDER_INVOICE_ID . ' = ?',
                $id
            );
        return $this;
    }
}
