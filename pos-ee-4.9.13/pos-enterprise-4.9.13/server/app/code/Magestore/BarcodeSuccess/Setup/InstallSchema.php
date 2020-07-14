<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('os_barcode'));
        $setup->getConnection()->dropTable($setup->getTable('os_barcode_created_history'));
        $setup->getConnection()->dropTable($setup->getTable('os_barcode_template'));

        /**
         * Create table 'os_barcode'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_barcode'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Barcode ID'
            )
            ->addColumn(
                'barcode',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Barcode'
            )
            ->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Qty'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Barcode ID'
            )
            ->addColumn(
                'product_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Barcode SKU'
            )
            ->addColumn(
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Supplier ID'
            )
            ->addColumn(
                'supplier_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Supplier Code'
            )
            ->addColumn(
                'purchased_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Purchased ID'
            )
            ->addColumn(
                'purchased_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Purchased Time'
            )
            ->addColumn(
                'history_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'History ID'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addIndex(
                $installer->getIdxName('os_barcode', ['id']),
                ['id']
            )
            ->addIndex(
                $installer->getIdxName('os_barcode', ['product_id']),
                ['product_id']
            )
            ->addIndex(
                $installer->getIdxName('os_barcode', ['product_sku']),
                ['product_sku']
            )
            ->addIndex(
                $installer->getIdxName('os_barcode', ['history_id']),
                ['history_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'os_barcode',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'os_barcode',
                    'product_sku',
                    'catalog_product_entity',
                    'sku'
                ),
                'product_sku',
                $installer->getTable('catalog_product_entity'),
                'sku',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('BarcodeSuccess Barcode Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'os_barcode_created_history'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_barcode_created_history'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'History ID'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Created By'
            )
            ->addColumn(
                'reason',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Reason'
            )
            ->addColumn(
                'total_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Total Qty'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Type'
            )
            ->addIndex(
                $installer->getIdxName('os_barcode_created_history', ['id']),
                ['id']
            )
            ->addIndex(
                $installer->getIdxName('os_barcode_created_history', ['created_by']),
                ['created_by']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'os_barcode_created_history',
                    'created_by',
                    'admin_user',
                    'user_id'
                ),
                'created_by',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('BarcodeSuccess History Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'os_barcode_template'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_barcode_template'))
            ->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Template ID'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Type'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Priority'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )
            ->addColumn(
                'symbology',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Symbology'
            )
            ->addColumn(
                'measurement_unit',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'Measurement Unit'
            )
            ->addColumn(
                'label_per_row',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Label Per Row'
            )
            ->addColumn(
                'paper_width',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Paper Width'
            )
            ->addColumn(
                'paper_height',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Paper Height'
            )
            ->addColumn(
                'label_width',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Label Width'
            )
            ->addColumn(
                'label_height',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Label Height'
            )
            ->addColumn(
                'font_size',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Font Size'
            )
            ->addColumn(
                'top_margin',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Top Margin'
            )
            ->addColumn(
                'left_margin',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Left Margin'
            )
            ->addColumn(
                'right_margin',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Right Margin'
            )
            ->addColumn(
                'bottom_margin',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Bottom Margin'
            )
            ->addIndex(
                $installer->getIdxName('os_barcode_template', ['template_id']),
                ['template_id']
            )
            ->addIndex(
                $installer->getIdxName('os_barcode_template', ['name']),
                ['name']
            )
            ->addIndex(
                $installer->getIdxName('os_barcode_template', ['type']),
                ['type']
            )
            ->addIndex(
                $installer->getIdxName('os_barcode_template', ['symbology']),
                ['symbology']
            )
            ->setComment('BarcodeSuccess Barcode Template Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
        return $this;
    }
}