<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder;

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

    const TABLE_RETURN_ORDER_ITEM = 'os_return_order_item';

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
        $this->_init(self::TABLE_RETURN_ORDER_ITEM, 'return_item_id');
    }

    /**
     * Add product to return order from return order products data
     *
     * @param array $returnProductsData
     * @return bool
     */
    public function addProductsToReturnOrder($returnProductsData = []){
        if(!count($returnProductsData))
            return true;
        $this->queryProcessor->start();
        $this->queryProcessor->addQuery([
            'type' => QueryProcessorInterface::QUERY_TYPE_INSERT,
            'values' => $returnProductsData,
            'table' => $this->getTable(self::TABLE_RETURN_ORDER_ITEM)
        ]);
        $this->queryProcessor->process();
        return true;
    }
}