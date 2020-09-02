<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Magestore\DropshipSuccess\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return $this
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('os_dropship_request'));
        $setup->getConnection()->dropTable($setup->getTable('os_dropship_request_item'));
        $setup->getConnection()->dropTable($setup->getTable('os_dropship_shipment'));
        $setup->getConnection()->dropTable($setup->getTable('os_dropship_shipment_item'));
        $setup->getConnection()->dropTable($setup->getTable('os_dropship_cancel'));
        $setup->getConnection()->dropTable($setup->getTable('os_dropship_cancel_item'));
        $setup->getConnection()->dropTable($setup->getTable('os_dropship_supplier_shipment'));
        $setup->getConnection()->dropTable($setup->getTable('os_dropship_supplier_pricelist_upload'));

        /**
         * Create table 'os_dropship_request'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_dropship_request'))
            ->addColumn(
                'dropship_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Dropship Request Id'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Sales ID'
            )->addColumn(
                'order_increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Sales Increment Id'
            )->addColumn(
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null],
                'Supplier ID'
            )->addColumn(
                'supplier_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Supplier Name'
            )->addColumn(
                'total_requested',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total requested qty'
            )->addColumn(
                'total_shipped',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total shipped qty'
            )->addColumn(
                'total_canceled',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total canceled qty'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'os_dropship_request_item'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_dropship_request_item'))
            ->addColumn(
                'dropship_request_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Dropship Request Item Id'
            )->addColumn(
                'dropship_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Dropship Request Id'
            )->addColumn(
                'parent_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null],
                'Parent Item id'
            )->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null],
                'Item id'
            )->addColumn(
                'item_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Item sku'
            )->addColumn(
                'item_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Item name'
            )->addColumn(
                'qty_requested',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Qty Requested'
            )->addColumn(
                'qty_shipped',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Qty shipped'
            )->addColumn(
                'qty_canceled',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Qty Canceled'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'os_dropship_shipment'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_dropship_shipment'))
            ->addColumn(
                'dropship_shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Dropship Shipment Id'
            )->addColumn(
                'dropship_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Dropship Request Id'
            )->addColumn(
                'shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Shipment Id'
            )->addColumn(
                'carrier_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Carrier Code'
            )->addColumn(
                'shipping_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Shipping label'
            )->addColumn(
                'track_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Track Number'
            )->addColumn(
                'total_shipped',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total shipped'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'os_dropship_shipment_item'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_dropship_shipment_item'))
            ->addColumn(
                'dropship_shipment_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Dropship Shipment item Id'
            )->addColumn(
                'dropship_shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Dropship Shipment Id'
            )->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Item id'
            )->addColumn(
                'item_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Item sku'
            )->addColumn(
                'item_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Item name'
            )->addColumn(
                'qty_shipped',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Qty shipped'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'os_dropship_supplier_shipment'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_dropship_supplier_shipment'))
            ->addColumn(
                'supplier_shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Supplier Shipment Id'
            )->addColumn(
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Supplier Id'
            )->addColumn(
                'shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Shipment Id'
            )->addColumn(
                'supplier_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Supplier Code'
            )->addColumn(
                'supplier_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Supplier Name'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'os_dropship_supplier_pricelist_upload'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_dropship_supplier_pricelist_upload'))
            ->addColumn(
                'supplier_pricelist_upload_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Supplier Pricelist Upload Id'
            )->addColumn(
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Supplier Id'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Title'
            )->addColumn(
                'file_upload',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'File Upload'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            );
        $installer->getConnection()->createTable($table);


        $installer->endSetup();
        return $this;
    }
}