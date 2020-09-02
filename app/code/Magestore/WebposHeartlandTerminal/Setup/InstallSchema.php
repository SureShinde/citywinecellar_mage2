<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposHeartlandTerminal\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magestore\Webpos\Api\Data\Pos\PosInterface;
use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Class InstallSchema
 *
 * @package Magestore\WebposHeartlandTerminal\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $setup->getTable(ConnectedReaderInterface::TABLE_ENTITY);
        $posTableName = $setup->getTable('webpos_pos');

        $table = $installer->getConnection()->newTable(
            $tableName
        )->addColumn(
            ConnectedReaderInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            ConnectedReaderInterface::ID
        )->addColumn(
            ConnectedReaderInterface::POS_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => 0],
            ConnectedReaderInterface::POS_ID
        )->addColumn(
            ConnectedReaderInterface::IP_ADDRESS,
            Table::TYPE_TEXT,
            32,
            ['nullable' => true, 'default' => ''],
            ConnectedReaderInterface::IP_ADDRESS
        )->addColumn(
            ConnectedReaderInterface::PORT,
            Table::TYPE_TEXT,
            32,
            ['nullable' => true, 'default' => ''],
            ConnectedReaderInterface::PORT
        )->addColumn(
            ConnectedReaderInterface::SERIAL_PORT,
            Table::TYPE_TEXT,
            32,
            ['nullable' => true, 'default' => ''],
            ConnectedReaderInterface::SERIAL_PORT
        )->addColumn(
            ConnectedReaderInterface::CONNECTION_MODE,
            Table::TYPE_TEXT,
            32,
            ['nullable' => true, 'default' => ''],
            ConnectedReaderInterface::CONNECTION_MODE
        )->addForeignKey(
            $setup->getFkName($tableName, ConnectedReaderInterface::POS_ID, $posTableName, PosInterface::POS_ID),
            ConnectedReaderInterface::POS_ID,
            $posTableName,
            PosInterface::POS_ID,
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
        return $this;
    }
}
