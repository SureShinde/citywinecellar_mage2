<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Magestore\AdjustStock\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('os_adjuststock'));
        $setup->getConnection()->dropTable($setup->getTable('os_adjuststock_product'));

        /**
         * create os_adjuststock table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_adjuststock'))
            ->addColumn(
                'adjuststock_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Adjuststock Id'
            )->addColumn(
                'adjuststock_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Adjuststock Code'
            )->addColumn(
                'source_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Source Name'
            )->addColumn(
                'source_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Source Code'
            )->addColumn(
                'reason',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Reason'
            )->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Created By'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Created At'
            )->addColumn(
                'confirmed_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Confirmed By'
            )->addColumn(
                'confirmed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Confirmed At'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => null],
                'Status'
            )->addIndex(
                $installer->getIdxName(
                    'os_adjuststock_adjuststock_code',
                    ['adjuststock_code'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['adjuststock_code'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $installer->getIdxName('os_adjuststock', ['source_code']),
                ['source_code']
            );
        $installer->getConnection()->createTable($table);

        /**
         * create  os_adjuststock_product table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_adjuststock_product'))
            ->addColumn(
                'adjuststock_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Adjuststock Product Id'
            )
            ->addColumn(
                'adjuststock_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Adjuststock Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Product Id'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product Name'
            )->addColumn(
                'product_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product SKU'
            )->addColumn(
                'old_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Old Qty'
            )->addColumn(
                'new_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'New Qty'
            )->addColumn(
                'change_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Change Qty'
            )->addIndex(
                $installer->getIdxName('os_adjuststock_product', ['adjuststock_id']),
                ['adjuststock_id']
            )->addIndex(
                $installer->getIdxName('os_adjuststock_product', ['product_id']),
                ['product_id']
            )->addForeignKey(
                $installer->getFkName(
                    'os_adjuststock_product',
                    'adjuststock_id',
                    'os_adjuststock',
                    'adjuststock_id'
                ),
                'adjuststock_id',
                $installer->getTable('os_adjuststock'),
                'adjuststock_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_adjuststock_product',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
        return $this;
    }


}
