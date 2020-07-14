<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Magestore\Webpos\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('webpos_staff'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_session'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_location'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_pos'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_customer_deleted'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_product_deleted'));

        $table = $installer->getConnection()->newTable(
            $installer->getTable('webpos_staff')
        )->addColumn(
            'staff_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'staff_id'
        )->addColumn(
            'role_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            'role_id'
        )->addColumn(
            'username',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'username'
        )->addColumn(
            'password',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'password'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'name'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'email'
        )->addColumn(
            'customer_groups',
            Table::TYPE_TEXT,
            127,
            ['nullable' => false, 'default' => ''],
            'customer_groups'
        )->addColumn(
            'location_ids',
            Table::TYPE_TEXT,
            127,
            ['nullable' => false, 'default' => ''],
            'location_ids'
        )->addColumn(
            'pos_ids',
            Table::TYPE_TEXT,
            127,
            ['nullable' => false, 'default' => ''],
            'pos_ids'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'status'
        );

        if ($installer->tableExists('webpos_authorization_role')) {
            $table->addForeignKey(
                $setup->getFkName('webpos_staff', 'role_id', 'webpos_authorization_role', 'role_id'),
                'role_id',
                $setup->getTable('webpos_authorization_role'),
                'role_id',
                Table::ACTION_CASCADE
            );
        }

        $installer->getConnection()->createTable($table);

        $webposSession = $installer->getConnection()->newTable(
            $installer->getTable('webpos_session')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'id'
        )->addColumn(
            'staff_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'staff_id'
        )->addColumn(
            'logged_date',
            Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'logged_date'
        )->addColumn(
            'session_id',
            Table::TYPE_TEXT,
            127,
            ['nullable' => true],
            'session_id'
        )->addColumn(
            'current_shift_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'current_shift_id'
        )->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'location_id'
        )->addColumn(
            'pos_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'pos_id'
        )->addForeignKey(
            $setup->getFkName('webpos_session', 'staff_id', 'webpos_staff', 'staff_id'),
            'staff_id',
            $setup->getTable('webpos_staff'),
            'staff_id',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($webposSession);

        $webposLocation = $installer->getConnection()->newTable(
            $installer->getTable('webpos_location')
        )->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'location_id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'name'
        )->addColumn(
            'address',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'address'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            Table::MAX_TEXT_SIZE,
            ['nullable' => true],
            'description'
        )->addColumn(
            'store_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'store_id'
        );

        $installer->getConnection()->createTable($webposLocation);

        $webposPos = $installer->getConnection()->newTable(
            $installer->getTable('webpos_pos')
        )->addColumn(
            'pos_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'pos_id'
        )->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'location_id'
        )->addColumn(
            'pos_name',
            Table::TYPE_TEXT,
            127,
            ['nullable' => true],
            'pos_name'
        )->addColumn(
            'staff_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'staff_id'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'status'
        )->addColumn(
            'denomination_ids',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'denomination_ids'
        )->addForeignKey(
            $setup->getFkName('webpos_pos', 'location_id', 'webpos_location', 'location_id'),
            'location_id',
            $setup->getTable('webpos_location'),
            'location_id',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($webposPos);

        $installer->endSetup();
        return $this;
    }
}