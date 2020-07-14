<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Magestore\Appadmin\Setup
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
        $setup->getConnection()->dropTable($setup->getTable('webpos_authorization_role'));
        $setup->getConnection()->dropTable($setup->getTable('webpos_authorization_rule'));

        $tableRole = $installer->getConnection()->newTable(
            $installer->getTable('webpos_authorization_role')
        )->addColumn(
            'role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'role_id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            127,
            ['nullable' => false, 'default' => ''],
            'name'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'description'
        )->addColumn(
            'maximum_discount_percent',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['nullable' => false, 'default' => 0],
            'maximum_discount_percent'
        );
        $installer->getConnection()->createTable($tableRole);

        $tableStaff = $installer->getConnection()->newTable(
            $installer->getTable('webpos_staff')
        )->addColumn(
            'staff_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'staff_id'
        )->addColumn(
            'role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            'role_id'
        )->addColumn(
            'username',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'username'
        )->addColumn(
            'password',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'password'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'name'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'email'
        )->addColumn(
            'customer_groups',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            127,
            ['nullable' => false, 'default' => ''],
            'customer_groups'
        )->addColumn(
            'location_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            127,
            ['nullable' => false, 'default' => ''],
            'location_ids'
        )->addColumn(
            'pos_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            127,
            ['nullable' => false, 'default' => ''],
            'pos_ids'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'status'
        )->addForeignKey(
            $setup->getFkName('webpos_staff', 'role_id', 'webpos_authorization_role', 'role_id'),
            'role_id',
            $setup->getTable('webpos_authorization_role'),
            'role_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($tableStaff);

        $tableAuthorizeRule = $installer->getConnection()->newTable(
            $installer->getTable('webpos_authorization_rule')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'rule_id'
        )->addColumn(
            'role_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            'role_id'
        )->addColumn(
            'resource_id',
            Table::TYPE_TEXT,
            '255',
            ['nullable' => true],
            'resource_id'
        )->addForeignKey(
            $setup->getFkName('webpos_authorization_rule', 'role_id', 'webpos_authorization_role', 'role_id'),
            'role_id',
            $setup->getTable('webpos_authorization_role'),
            'role_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($tableAuthorizeRule);


        $installer->endSetup();
        return $this;
    }
}