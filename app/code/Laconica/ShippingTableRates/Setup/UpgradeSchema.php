<?php

namespace Laconica\ShippingTableRates\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('amasty_table_method'),
                'is_tips',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0',
                    'comment' => 'Is Tips'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_shipping_rate'),
                'is_tips',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0',
                    'comment' => 'Is Tips'
                ]
            );
        }
    }
}
