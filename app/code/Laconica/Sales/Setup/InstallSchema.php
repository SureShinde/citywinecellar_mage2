<?php

namespace Laconica\Sales\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $connection->modifyColumn(
            $installer->getTable('sales_order_grid'),
            'pos_location_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'length' => '10',
                'default' => '0',
                'comment' => 'Pos Location ID'
            ]
        );

        $installer->endSetup();
    }
}