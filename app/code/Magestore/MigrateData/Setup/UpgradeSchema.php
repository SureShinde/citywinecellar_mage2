<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\MigrateData\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 *
 * @package Magestore\MigrateData\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    
    /**
     * Upgrade
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('os_supplier'),
                'account_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Account Number'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('os_supplier'),
                'tax_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Tax Number'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('os_supplier'),
                'terms',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Terms'
                ]
            );
        }
        
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('os_supplier_product'),
                'minimum_order',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Minimum Order'
                ]
            );
        }
    
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('os_supplier'),
                'street_2',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Street 2'
                ]
            );
        }
        
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('os_supplier_product'),
                'master_pack_quantity',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Master Pack Quantity'
                ]
            );
        }
        
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('os_supplier_product'),
                'item_lookup_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Item Lookup Code'
                ]
            );
        }
        
        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'microsoft_order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Microsoft Order Id'
                ]
            );
        }
    
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('microsoft_customer_info')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'id'
            )->addColumn(
                'website',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Website'
            )->addColumn(
                'ms_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Microsoft customer id'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Email'
            )->addColumn(
                'firstname',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'First Name'
            )->addColumn(
                'lastname',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Last Name'
            )->addColumn(
                'company',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Company'
            )->addColumn(
                'country',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Country'
            )->addColumn(
                'state',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'State'
            )->addColumn(
                'address',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'address'
            )->addColumn(
                'city',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'city'
            )->addColumn(
                'zipcode',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'zipcode'
            )->addColumn(
                'tax_number',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'tax_number'
            )->addColumn(
                'phone',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'phone'
            )->addColumn(
                'fax_number',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'fax_number'
            );
    
            $setup->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('microsoft_order_refund')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'id'
            )->addColumn(
                'website',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Website'
            )->addColumn(
                'microsoft_order_refund_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'microsoft_order_refund_id'
            )->addColumn(
                'microsoft_order_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'microsoft_order_id'
            );
    
            $setup->getConnection()->createTable($table);
        }
    
        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('microsoft_product_mapping')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'id'
            )->addColumn(
                'magento_product_sku',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'magento_product_sku'
            )->addColumn(
                'ms_product_id',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Microsoft product id'
            )->addColumn(
                'product_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Product Name'
            );
        
            $setup->getConnection()->createTable($table);
        }
    
        if (version_compare($context->getVersion(), '1.0.10', '<')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('microsoft_order_item_imported')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'id'
            )->addColumn(
                'website',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Website'
            )->addColumn(
                'microsoft_order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'microsoft_order_item_id'
            );
        
            $setup->getConnection()->createTable($table);
        }
    }
}
