<?php

namespace Magestore\PaymentOffline\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Magestore\PaymentOffline\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $webposPaymentOffline = $installer->getConnection()->newTable(
            $installer->getTable('webpos_payment_offline')
        )->addColumn(
            'payment_offline_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'location_id'
        )->addColumn(
            'payment_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'payment code'
        )->addColumn(
            'enable',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'enable'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'title'
        )->addColumn(
            'use_reference_number',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'use reference number'
        )->addColumn(
            'icon_type',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'icon type'
        )->addColumn(
            'icon_path',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'icon path'
        )->addColumn(
            'use_pay_later',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'use pay later'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'sort order'
        );

        $installer->getConnection()->createTable($webposPaymentOffline);

        $installer->endSetup();
    }
}