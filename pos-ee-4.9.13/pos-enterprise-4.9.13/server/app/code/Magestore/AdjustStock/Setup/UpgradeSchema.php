<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Cms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $setup->getConnection()->dropTable($setup->getTable('os_increment_id'));

            /**
             * create os_increment_id table
             */
            $table  = $installer->getConnection()
                ->newTable($installer->getTable('os_increment_id'))
                ->addColumn(
                    'increment_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Increment Id'
                )->addColumn(
                    'code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['unique' => true, 'nullable' => false],
                    'Entity Type Code'
                )->addColumn(
                    'current_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['default' => 1, 'unsigned' => true],
                    'Current Id'
                )->addIndex(
                    $installer->getIdxName('os_increment_id', ['increment_id']),
                    ['increment_id']
                );
            $installer->getConnection()->createTable($table);

        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('os_adjuststock_product'),
                'barcode',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Barcode'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->removeFkToCatalogProductEntityTable($installer);
        }
    }

    /**
     * Remove foreign key reference to catalog_product_entity
     *
     * @param SchemaSetupInterface $setup
     * */
    private function removeFkToCatalogProductEntityTable (SchemaSetupInterface $setup)
    {
        $setup->getConnection()->dropForeignKey(
            $setup->getTable('os_adjuststock_product'),
            $setup->getFkName(
                'os_adjuststock_product',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            )
        );
    }
}
