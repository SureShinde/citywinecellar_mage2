<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder;

use Magestore\PurchaseOrderSuccess\Model\Db\QueryProcessorInterface;

/**
 * Class Item
 * @package Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder
 */
class Item extends \Magestore\PurchaseOrderSuccess\Model\ResourceModel\AbstractResource
{
    /**
     * @var QueryProcessorInterface
     */
    protected $queryProcessor;

    const TABLE_PURCHASE_ORDER_ITEM = 'os_purchase_order_item';

    /**
     * Item constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param QueryProcessorInterface $queryProcessor
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        QueryProcessorInterface $queryProcessor,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->queryProcessor = $queryProcessor;
    }

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_PURCHASE_ORDER_ITEM, 'purchase_order_item_id');
    }

    /**
     * Add product to purchase order from purchase order products data
     * 
     * @param array $purchaseProductsData
     * @return bool
     */
    public function addProductsToPurchaseOrder($purchaseProductsData = []){
        if(!count($purchaseProductsData))
            return true;
        $this->queryProcessor->start();
        $this->queryProcessor->addQuery([
            'type' => QueryProcessorInterface::QUERY_TYPE_INSERT,
            'values' => $purchaseProductsData,
            'table' => $this->getTable(self::TABLE_PURCHASE_ORDER_ITEM)
        ]);
        $this->queryProcessor->process();
        return true;
    }
}