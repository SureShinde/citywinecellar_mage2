<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Supplier;

use Magestore\SupplierSuccess\Model\Db\QueryProcessorInterface;

/**
 * Class Product
 *
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    /**
     * @var QueryProcessorInterface
     */
    protected $queryProcessor;

    /**
     * Product constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param QueryProcessorInterface $queryProcessor
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $connectionName
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        QueryProcessorInterface $queryProcessor,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->eavConfig = $eavConfig;
        $this->queryProcessor = $queryProcessor;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('os_supplier_product', 'supplier_product_id');
    }

    /**
     * Add product to supplier
     *
     * $data = [
     *  'supplier_id' => int,
     *  'product_id' => int,
     *  'product_sku' => string,
     *  'product_name' => string,
     *  'product_supplier_sku' => string,
     *  'cost' => float,
     *  'tax' => float
     * ]
     *
     * @param array $data
     * @return int
     */
    public function addProducts($data)
    {
        $connection = $this->getConnection();
        $table = $this->getTable('os_supplier_product');
        return $connection->insertOnDuplicate($table, $data);
    }

    /**
     * Correct products information
     */
    public function correctProductInfo()
    {
        $connection = $this->getConnection();

        /* Get all magento product with name */
        $productSelect = $this->getConnection()->select()
            ->from(
                ['e' => $this->getTable('catalog_product_entity')]
            );
        /** Join with attribute name */
        $productEntityId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
        $productSelect->joinLeft(
            ['ea' => $this->getTable('eav_attribute')],
            "ea.entity_type_id = $productEntityId AND ea.attribute_code = 'name'",
            []
        );
        $productSelect->joinLeft(
            ['cpev' => $this->getTable('catalog_product_entity_varchar')],
            "cpev.entity_id = e.entity_id AND cpev.attribute_id = ea.attribute_id",
            [
                'name' => 'value'
            ]
        );

        /* Filter invalid supplier products */
        $supplierProduct = $connection->select()->from(
            ['main_table' => $this->getMainTable()],
            ['supplier_product_id']
        );
        $supplierProduct->joinInner(
            ['core' => $productSelect],
            new \Zend_Db_Expr(
                'main_table.product_id = core.entity_id AND ' .
                '(main_table.product_sku != core.sku OR main_table.product_name != core.name)'
            ),
            ['sku', 'name']
        );

        $updatedData = $connection->fetchAll($supplierProduct);
        if (!count($updatedData)) {
            return;
        }

        $this->queryProcessor->start('supplierProductUpdate');

        $where = ['supplier_product_id IN (?)' => array_column($updatedData, 'supplier_product_id')];

        $values = [];
        $conditions = [];
        foreach ($updatedData as $datum) {
            $case = $connection->quoteInto('?', $datum['supplier_product_id']);
            $conditions['product_sku'][$case] = $connection->quoteInto('?', $datum['sku']);
            $conditions['product_name'][$case] = $connection->quoteInto('?', $datum['name']);
        }
        /* bind conditions to $updateValues */
        foreach ($conditions as $field => $condition) {
            $values[$field] = $connection->getCaseSql('supplier_product_id', $condition, $field);
        }

        $this->queryProcessor->addQuery(
            [
                'type' => QueryProcessorInterface::QUERY_TYPE_UPDATE,
                'values' => $values,
                'condition' => $where,
                'table' => $this->getMainTable()
            ],
            'supplierProductUpdate'
        );

        $this->queryProcessor->process('supplierProductUpdate');
    }
}
