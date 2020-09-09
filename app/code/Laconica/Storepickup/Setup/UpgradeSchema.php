<?php

namespace Laconica\Storepickup\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $table = $installer->getTable('sales_order');
            $installer->getConnection()->addColumn(
                $table,
                'tips',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '7,2',
                    'nullable' => true,
                    'comment' => 'Handling surcharge',
                    'default' => 0
                ]
            );

            $table = $installer->getTable('quote');
            $installer->getConnection()->addColumn(
                $table,
                'tips',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '7,2',
                    'nullable' => true,
                    'comment' => 'Handling surcharge',
                    'default' => 0
                ]
            );
        }

        $installer->endSetup();
    }
}
