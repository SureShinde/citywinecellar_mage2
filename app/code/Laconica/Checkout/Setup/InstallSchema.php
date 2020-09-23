<?php

namespace Laconica\Checkout\Setup;

use Laconica\Checkout\Helper\StateConfig;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists(StateConfig::ZIP_STATE_CONNECTION_TABLE)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable(StateConfig::ZIP_STATE_CONNECTION_TABLE)
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'zip_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'ZIP Code'
                )
                ->addColumn(
                    'region_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Region code'
                )
                ->addColumn(
                    'region_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [],
                    'Region ID'
                )
                ->setComment('US ZIP To Region connection');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}