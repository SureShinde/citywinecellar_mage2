<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel\AdjustStock;

use Magestore\AdjustStock\Api\Data\AdjustStock\AdjustStockInterface;
use Magestore\AdjustStock\Api\Db\QueryProcessorInterface;

/**
 * Class Product
 *
 * @package Magestore\AdjustStock\Model\ResourceModel\AdjustStock
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var QueryProcessorInterface
     */
    protected $queryProcessor;

    /**
     * Product constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param QueryProcessorInterface $queryProcessor
     * @param string|null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        QueryProcessorInterface $queryProcessor,
        $connectionName = null
    ) {
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
        $this->_init('os_adjuststock_product', 'adjuststock_product_id');
    }

    /**
     * Correct current qty in source of an adjust stock request
     *
     * @param AdjustStockInterface $adjustStock
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function correctCurrectQty($adjustStock)
    {
        $connection = $this->getConnection();

        $adjustProductSelect = $connection->select()->from(
            ['main' => $this->getMainTable()],
            ['adjuststock_product_id', 'change_qty']
        );
        $adjustProductSelect->where('main.adjuststock_id = ?', $adjustStock->getId());
        $adjustProductSelect->joinInner(
            ['source_product' => $this->getTable('inventory_source_item')],
            new \Zend_Db_Expr(
                "main.product_sku = source_product.sku AND " .
                "source_product.source_code = '" . $adjustStock->getSourceCode() . "'"
            ),
            ['current_qty' => 'source_product.quantity']
        );
        $adjustProductSelect->where(new \Zend_Db_Expr("main.old_qty <> source_product.quantity"));

        $incorrectAdjustProduct = $connection->fetchAll($adjustProductSelect);

        if (count($incorrectAdjustProduct)) {
            $this->updateCurrentProductQuantity($incorrectAdjustProduct, $adjustStock);
        }
    }

    /**
     * Correct quantity
     *
     * @param array $data
     * @param AdjustStockInterface $adjustStock
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCurrentProductQuantity($data, $adjustStock)
    {
        $connection = $this->getConnection();
        $this->queryProcessor->start('adjustStockProductUpdate');

        $where = ['adjuststock_id = ?' => $adjustStock->getId()];

        $values = [];
        $conditions = [];
        foreach ($data as $datum) {
            $case = $connection->quoteInto('?', $datum['adjuststock_product_id']);
            $conditions['old_qty'][$case] = $connection->quoteInto('?', $datum['current_qty']);
            $conditions['new_qty'][$case] = $connection->quoteInto(
                '?',
                (float)$datum['current_qty'] + (float)$datum['change_qty']
            );
        }
        /* bind conditions to $updateValues */
        foreach ($conditions as $field => $condition) {
            $values[$field] = $connection->getCaseSql('adjuststock_product_id', $condition, $field);
        }

        $this->queryProcessor->addQuery(
            [
                'type' => QueryProcessorInterface::QUERY_TYPE_UPDATE,
                'values' => $values,
                'condition' => $where,
                'table' => $this->getMainTable()
            ],
            'adjustStockProductUpdate'
        );

        $this->queryProcessor->process('adjustStockProductUpdate');
    }
}
