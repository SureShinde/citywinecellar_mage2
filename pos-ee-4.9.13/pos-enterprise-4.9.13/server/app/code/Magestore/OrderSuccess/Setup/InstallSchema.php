<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Setup;
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

        $setup->getConnection()->dropTable($setup->getTable('os_ordersuccess_batch'));

        /**
         * create os_ordersuccess_batch table
         */
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_ordersuccess_batch'))
            ->addColumn(
                'batch_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Batch Id'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default' => null],
                'Code'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => true],
                'User Id'
            )->addIndex(
                $installer->getIdxName('os_ordersuccess_batch', ['user_id']),
                ['user_id']
            )->addForeignKey(
                $installer->getFkName(
                    'os_ordersuccess_batch',
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
         * add columns to sales_order table
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'tag_color',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable'  => true,
                'length'    => 255,
                'default'    => '',
                'comment'   => 'Tag Color'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'is_verified',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'  => 0,
                'length'    => 1,
                'comment'   => 'Is Verified'
            )
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'batch_id',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'  => 0,
                'length'    => 11,
                'unsigned' => true,
                'comment'   => 'Batch ID'
            )
        );

        /**
         * add columns to sales_order_item table
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'qty_prepareship',
            array(
                'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable'  => false,
                'length'    => '12,4',
                'default'    => 0,
                'comment'   => 'Qty Prepareship'
            )
        );

        $installer->endSetup();
        return $this;
        }


}