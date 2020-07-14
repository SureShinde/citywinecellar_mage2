<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Magestore\OrderSuccess\Setup
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

        $setup->getConnection()->dropTable($setup->getTable('os_fulfilsuccess_batch'));
        $setup->getConnection()->dropTable($setup->getTable('os_fulfilsuccess_pickrequest'));
        $setup->getConnection()->dropTable($setup->getTable('os_fulfilsuccess_pickrequest_item'));

        /**
         * Drop Pack request tables
         */
        $setup->getConnection()->dropTable($setup->getTable('os_fulfilsuccess_packrequest'));
        $setup->getConnection()->dropTable($setup->getTable('os_fulfilsuccess_packrequest_item'));

        /**
         * Drop Package tables
         */
        $setup->getConnection()->dropTable($setup->getTable('os_fulfilsuccess_package'));
        $setup->getConnection()->dropTable($setup->getTable('os_fulfilsuccess_package_item'));

        /**
         * create os_fulfilsuccess_batch table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_fulfilsuccess_batch'))
            ->addColumn(
                'batch_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Warehouse Product Id'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Batch Number'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Created By User'
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_fulfilsuccess_pickrequest table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_fulfilsuccess_pickrequest'))
            ->addColumn(
                'pick_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Pick Request Id'
            )->addColumn(
                'pack_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => true],
                'Pack Request Id'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Sales Sales Id'
            )->addColumn(
                'order_increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['default' => null, 'unsigned' => true],
                'Sales Sales Increment Id'
            )->addColumn(
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Warehouse Id'
            )->addColumn(
                'age',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Age Pick Request'
            )->addColumn(
                'batch_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => 0, 'unsigned' => true],
                'Batch Id'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'User Id'
            )->addColumn(
                'total_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Total Items'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => 0],
                'Status'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_pickrequest',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_pickrequest',
                    'warehouse_id',
                    'os_warehouse',
                    'warehouse_id'
                ),
                'warehouse_id',
                $installer->getTable('os_warehouse'),
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_fulfilsuccess_pickrequest_item table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_fulfilsuccess_pickrequest_item'))
            ->addColumn(
                'pick_request_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Pick Request Item Id'
            )->addColumn(
                'pick_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Pick Request Id'
            )->addColumn(
                'parent_pick_request_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Parent Pick Request Item Id'
            )->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Sales Item Id'
            )->addColumn(
                'parent_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Parent Sales Item Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Product Id'
            )->addColumn(
                'item_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null, 'unsigned' => true],
                'Sales Item Sku'
            )->addColumn(
                'item_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null, 'unsigned' => true],
                'Sales Item Name'
            )->addColumn(
                'item_barcode',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Barcode'
            )->addColumn(
                'request_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => 0],
                'Request Qty'
            )->addColumn(
                'picked_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => 0],
                'Picked Qty'
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_pickrequest_item',
                    'pick_request_id',
                    'os_fulfilsuccess_pickrequest',
                    'pick_request_id'
                ),
                'pick_request_id',
                $installer->getTable('os_fulfilsuccess_pickrequest'),
                'pick_request_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_pickrequest_item',
                    'item_id',
                    'sales_order_item',
                    'item_id'
                ),
                'item_id',
                $installer->getTable('sales_order_item'),
                'item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_fulfilsuccess_packrequest table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_fulfilsuccess_packrequest'))
            ->addColumn(
                'pack_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Pack Request Id'
            )->addColumn(
                'pick_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Pick Request Id'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'User Id'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Sales Sales Id'
            )->addColumn(
                'order_increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['default' => null, 'unsigned' => true],
                'Sales Sales Increment Id'
            )->addColumn(
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Warehouse Id'
            )->addColumn(
                'age',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Age Pack Request'
            )->addColumn(
                'total_items',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Total Items'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => null],
                'Status'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_packrequest',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_packrequest',
                    'warehouse_id',
                    'os_warehouse',
                    'warehouse_id'
                ),
                'warehouse_id',
                $installer->getTable('os_warehouse'),
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_packrequest',
                    'pick_request_id',
                    'os_fulfilsuccess_pickrequest',
                    'pick_request_id'
                ),
                'pick_request_id',
                $installer->getTable('os_fulfilsuccess_pickrequest'),
                'pick_request_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_fulfilsuccess_packrequest_item table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_fulfilsuccess_packrequest_item'))
            ->addColumn(
                'pack_request_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Pack Request Item Id'
            )->addColumn(
                'pack_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Pack Request Id'
            )->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Sales Item Id'
            )->addColumn(
                'parent_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Parent Sales Item Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Product Id'
            )->addColumn(
                'item_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null, 'unsigned' => true],
                'Sales Item Sku'
            )->addColumn(
                'item_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null, 'unsigned' => true],
                'Sales Item Name'
            )->addColumn(
                'item_barcode',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Barcode'
            )->addColumn(
                'request_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => 0],
                'Request Qty'
            )->addColumn(
                'packed_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => 0],
                'Packed Qty'
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_packrequest_item',
                    'pack_request_id',
                    'os_fulfilsuccess_packrequest',
                    'pack_request_id'
                ),
                'pack_request_id',
                $installer->getTable('os_fulfilsuccess_packrequest'),
                'pack_request_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_packrequest_item',
                    'item_id',
                    'sales_order_item',
                    'item_id'
                ),
                'item_id',
                $installer->getTable('sales_order_item'),
                'item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create os_fulfilsuccess_package table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_fulfilsuccess_package'))
            ->addColumn(
                'package_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Package Id'
            )->addColumn(
                'pack_request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Pack Request Id'
            )->addColumn(
                'shipment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Shipment Id'
            )->addColumn(
                'track_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Track Id'
            )->addColumn(
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Warehouse Id'
            )->addColumn(
                'container',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Container'
            )->addColumn(
                'custom_value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Custom Value'
            )->addColumn(
                'weight',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Weight'
            )->addColumn(
                'length',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Length'
            )->addColumn(
                'width',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Width'
            )->addColumn(
                'height',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Height'
            )->addColumn(
                'weight_units',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Weight Units'
            )->addColumn(
                'dimension_units',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Dimension Units'
            )->addColumn(
                'content_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Content Type'
            )->addColumn(
                'content_type_other',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Content Type Other'
            )->addColumn(
                'delivery_confirmation',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Delivery Confirmation'
            )->addColumn(
                'image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Image'
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_package',
                    'pack_request_id',
                    'os_fulfilsuccess_packrequest',
                    'pack_request_id'
                ),
                'pack_request_id',
                $installer->getTable('os_fulfilsuccess_packrequest'),
                'pack_request_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_DEFAULT
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_package',
                    'shipment_id',
                    'sales_shipment',
                    'entity_id'
                ),
                'shipment_id',
                $installer->getTable('sales_shipment'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_DEFAULT
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_package',
                    'warehouse_id',
                    'os_warehouse',
                    'warehouse_id'
                ),
                'warehouse_id',
                $installer->getTable('os_warehouse'),
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_package',
                    'track_id',
                    'sales_shipment_track',
                    'entity_id'
                ),
                'track_id',
                $installer->getTable('sales_shipment_track'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_DEFAULT
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_fulfilsuccess_package_item table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_fulfilsuccess_package_item'))
            ->addColumn(
                'package_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Package Item Id'
            )->addColumn(
                'package_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => null, 'unsigned' => true],
                'Package Id'
            )->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => 0],
                'Qty'
            )->addColumn(
                'customs_value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Custom Value'
            )->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => 0],
                'Price'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Name'
            )->addColumn(
                'weight',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => 0],
                'Weight'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => 0],
                'Product ID'
            )->addColumn(
                'order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false, 'default' => 0],
                'Sales Item ID'
            )->addForeignKey(
                $installer->getFkName(
                    'os_fulfilsuccess_package_item',
                    'package_id',
                    'os_fulfilsuccess_package',
                    'package_id'
                ),
                'package_id',
                $installer->getTable('os_fulfilsuccess_package'),
                'package_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * add columns to sales_shipment table
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_shipment'),
            'fulfil_status',
            array(
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'length' => 5,
                'default' => 0,
                'comment' => 'Fulfil Status. 0: Did not give to carrier, 1: Gave to carrier'
            )
        );

        $installer->endSetup();
        return $this;
    }

}