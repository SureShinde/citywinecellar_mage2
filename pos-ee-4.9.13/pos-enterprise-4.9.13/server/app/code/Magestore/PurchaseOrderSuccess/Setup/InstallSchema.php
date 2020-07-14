<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Magestore\PurchaseOrderSuccess\Setup
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

        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_code'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_item'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_item_received'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_item_transferred'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_item_returned'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_invoice'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_invoice_item'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_invoice_payment'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_invoice_refund'));
        $setup->getConnection()->dropTable($setup->getTable('os_purchase_order_history'));

        /**
         * create os_purchase_order table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order'))
            ->addColumn(
                'purchase_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Id'
            )->addColumn(
                'purchase_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Purchase Code'
            )->addColumn(
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Supplier Id'
            )->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => 1],
                'Type'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => 0],
                'Status'
            )->addColumn(
                'send_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => 0],
                'Send Email To Supplier'
            )->addColumn(
                'is_sent',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => 0],
                'Is Sent Email To Supplier'
            )->addColumn(
                'comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Comment'
            )->addColumn(
                'shipping_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => ''],
                'Shipping Address'
            )->addColumn(
                'shipping_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Shipping Method'
            )->addColumn(
                'shipping_cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Shipping Cost'
            )->addColumn(
                'payment_term',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Payment Term'
            )->addColumn(
                'placed_via',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                4,
                ['default' => 0],
                'Sales Placed Via'
            )->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Created By'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'User Id'
            )->addColumn(
                'total_qty_orderred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Total Qty Orderred'
            )->addColumn(
                'total_qty_received',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Total Qty Received'
            )->addColumn(
                'total_qty_transferred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Total Qty Transferred'
            )->addColumn(
                'total_qty_returned',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Total Qty Returned'
            )->addColumn(
                'total_qty_billed',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Total Qty Billed'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Subtotal'
            )->addColumn(
                'total_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Total Tax'
            )->addColumn(
                'total_discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Total Discount'
            )->addColumn(
                'grand_total_excl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Grand Total Exclude Tax'
            )->addColumn(
                'grand_total_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Grand Total Include Tax'
            )->addColumn(
                'total_billed',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Total Billed'
            )->addColumn(
                'total_due',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => null],
                'Total Due'
            )->addColumn(
                'currency_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default' => null],
                'Currency Code'
            )->addColumn(
                'currency_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 1],
                'Currency Rate'
            )->addColumn(
                'purchased_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Purchased Sales Date'
            )->addColumn(
                'started_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Started Shipping Date'
            )->addColumn(
                'expected_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Expected Delevery Date'
            )->addColumn(
                'canceled_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Canceled Date'
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
                    'os_purchase_order',
                    'supplier_id',
                    'os_supplier',
                    'supplier_id'
                ),
                'supplier_id',
                $installer->getTable('os_supplier'),
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order',
                    'user_id',
                    'admin_user',
                    'user_id'
                ),
                'user_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_purchase_order_item table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_code'))
            ->addColumn(
                'purchase_order_code_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Code Id'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                11,
                ['nullable' => false],
                'Code'
            )->addColumn(
                'current_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Current Id'
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_purchase_order_item table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_item'))
            ->addColumn(
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Item Id'
            )->addColumn(
                'purchase_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Product Id'
            )->addColumn(
                'product_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product SKU'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product Name'
            )->addColumn(
                'product_supplier_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product Supplier SKU'
            )->addColumn(
                'qty_orderred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Orderred Qty'
            )->addColumn(
                'qty_received',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Received Qty'
            )->addColumn(
                'qty_transferred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Transferred Qty'
            )->addColumn(
                'qty_returned',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Returned Qty'
            )->addColumn(
                'qty_billed',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Billed Qty'
            )->addColumn(
                'original_cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Original Cost'
            )->addColumn(
                'cost',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Cost'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['unsigned' => true, 'default' => 0],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['unsigned' => true, 'default' => 0],
                'Discount'
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
            )->addIndex(
                $installer->getIdxName('os_purchase_order_item', 'purchase_order_id'),
                'purchase_order_id'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_item', 'product_id'),
                'product_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_item',
                    'purchase_order_id',
                    'os_purchase_order',
                    'purchase_order_id'
                ),
                'purchase_order_id',
                $installer->getTable('os_purchase_order'),
                'purchase_order_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_item',
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

        /**
         * create os_purchase_order_item_received table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_item_received'))
            ->addColumn(
                'purchase_order_item_received_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Item Received Id'
            )->addColumn(
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Item Id'
            )->addColumn(
                'qty_received',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Received Qty'
            )->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Created By'
            )->addColumn(
                'received_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Received At'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_item_received', 'purchase_order_item_id'),
                'purchase_order_item_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_item_received',
                    'purchase_order_item_id',
                    'os_purchase_order_item',
                    'purchase_order_item_id'
                ),
                'purchase_order_item_id',
                $installer->getTable('os_purchase_order_item'),
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_purchase_order_item_transferred table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_item_transferred'))
            ->addColumn(
                'purchase_order_item_transferred_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Item Transferred Id'
            )->addColumn(
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Item Id'
            )->addColumn(
                'qty_transferred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Transferred Qty'
            )->addColumn(
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '12,4',
                ['nullable' => false, 'unsigned' => true],
                'Warehouse Id'
            )->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Created By'
            )->addColumn(
                'transferred_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Transferred At'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_item_transferred', 'purchase_order_item_id'),
                'purchase_order_item_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_item_transferred',
                    'purchase_order_item_id',
                    'os_purchase_order_item',
                    'purchase_order_item_id'
                ),
                'purchase_order_item_id',
                $installer->getTable('os_purchase_order_item'),
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_purchase_order_item_returned table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_item_returned'))
            ->addColumn(
                'purchase_order_item_returned_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Item Returned Id'
            )->addColumn(
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Item Id'
            )->addColumn(
                'qty_returned',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Transferred Qty'
            )->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Created By'
            )->addColumn(
                'returned_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Returned At'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_item_returned', 'purchase_order_item_id'),
                'purchase_order_item_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_item_returned',
                    'purchase_order_item_id',
                    'os_purchase_order_item',
                    'purchase_order_item_id'
                ),
                'purchase_order_item_id',
                $installer->getTable('os_purchase_order_item'),
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_purchase_order_invoice table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_invoice'))
            ->addColumn(
                'purchase_order_invoice_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Invoice Id'
            )->addColumn(
                'purchase_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Id'
            )->addColumn(
                'billed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Billed At'
            )->addColumn(
                'total_qty_billed',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total Qty Billed'
            )->addColumn(
                'subtotal',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Subtotal'
            )->addColumn(
                'total_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total Tax'
            )->addColumn(
                'total_discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total Discount'
            )->addColumn(
                'grand_total_excl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Grand Total Exclude Tax'
            )->addColumn(
                'grand_total_incl_tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Grand Total Include Tax'
            )->addColumn(
                'total_due',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total Due'
            )->addColumn(
                'total_refund',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Total Refund'
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
            )->addIndex(
                $installer->getIdxName('os_purchase_order_invoice', 'purchase_order_id'),
                'purchase_order_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_invoice',
                    'purchase_order_id',
                    'os_purchase_order',
                    'purchase_order_id'
                ),
                'purchase_order_id',
                $installer->getTable('os_purchase_order'),
                'purchase_order_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_purchase_order_invoice_item table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_invoice_item'))
            ->addColumn(
                'purchase_order_invoice_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Invoice Item Id'
            )->addColumn(
                'purchase_order_invoice_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Invoice Id'
            )->addColumn(
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Item Id'
            )->addColumn(
                'qty_billed',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Billed Qty'
            )->addColumn(
                'unit_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Unit Price'
            )->addColumn(
                'tax',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['default' => 0],
                'Tax'
            )->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['default' => 0],
                'Discount'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_invoice_item', 'purchase_order_invoice_id'),
                'purchase_order_invoice_id'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_invoice_item', 'purchase_order_item_id'),
                'purchase_order_item_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_invoice_item',
                    'purchase_order_invoice_id',
                    'os_purchase_order_invoice',
                    'purchase_order_invoice_id'
                ),
                'purchase_order_invoice_id',
                $installer->getTable('os_purchase_order_invoice'),
                'purchase_order_invoice_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_invoice_item',
                    'purchase_order_item_id',
                    'os_purchase_order_item',
                    'purchase_order_item_id'
                ),
                'purchase_order_item_id',
                $installer->getTable('os_purchase_order_item'),
                'purchase_order_item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        /**
         * create os_purchase_order_invoice_payment table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_invoice_payment'))
            ->addColumn(
                'purchase_order_invoice_payment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Invoice Item Id'
            )->addColumn(
                'purchase_order_invoice_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Invoice Id'
            )->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Payment Method'
            )->addColumn(
                'payment_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Payment Amount'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Description'
            )->addColumn(
                'payment_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Payment Date'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_invoice_payment', 'purchase_order_invoice_id'),
                'purchase_order_invoice_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_invoice_payment',
                    'purchase_order_invoice_id',
                    'os_purchase_order_invoice',
                    'purchase_order_invoice_id'
                ),
                'purchase_order_invoice_id',
                $installer->getTable('os_purchase_order_invoice'),
                'purchase_order_invoice_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
        
        /**
         * create os_purchase_order_invoice_refund table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_invoice_refund'))
            ->addColumn(
                'purchase_order_invoice_refund_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales Invoice Refund Id'
            )->addColumn(
                'purchase_order_invoice_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Invoice Id'
            )->addColumn(
                'refund_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Refund Amount'
            )->addColumn(
                'reason',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Reason'
            )->addColumn(
                'refund_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Refund Date'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_invoice_refund', 'purchase_order_invoice_id'),
                'purchase_order_invoice_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_invoice_refund',
                    'purchase_order_invoice_id',
                    'os_purchase_order_invoice',
                    'purchase_order_invoice_id'
                ),
                'purchase_order_invoice_id',
                $installer->getTable('os_purchase_order_invoice'),
                'purchase_order_invoice_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
        
        /**
         * create os_purchase_order_history table
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('os_purchase_order_history'))
            ->addColumn(
                'purchase_order_history_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Purchase Sales History Id'
            )->addColumn(
                'purchase_order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Purchase Sales Id'
            )->addColumn(
                'user_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'User Name'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'User Id'
            )->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => ''],
                'Reason'
            )->addColumn(
                'old_value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => ''],
                'Old Value'
            )->addColumn(
                'new_value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => ''],
                'New Value'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_purchase_order_history', 'purchase_order_id'),
                'purchase_order_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_history',
                    'purchase_order_id',
                    'os_purchase_order',
                    'purchase_order_id'
                ),
                'purchase_order_id',
                $installer->getTable('os_purchase_order'),
                'purchase_order_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'os_purchase_order_history',
                    'user_id',
                    'admin_user',
                    'user_id'
                ),
                'user_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
        
        $installer->endSetup();
        return $this;
    }


}