<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Setup;

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
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addAttributeField($setup);
        }
        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $this->changeForeignKeyBarcode($setup);
        }

        if (version_compare($context->getVersion(), '1.3.3', '<')) {
            $installer->getConnection()->changeColumn(
                $installer->getTable('os_barcode'),
                'purchased_time',
                'purchased_time',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'comment' => 'Purchased Time'
                ]
            );
        }
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function addAttributeField(SchemaSetupInterface $setup)
    {
        if (!$setup->getConnection()->tableColumnExists($setup->getTable('os_barcode_template'), 'product_attribute_show_on_barcode')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('os_barcode_template'),
                'product_attribute_show_on_barcode',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Attributes'
                ]
            );
        }
        return $this;
    }

    public function changeForeignKeyBarcode(SchemaSetupInterface $setup) {
        $setup->getConnection()->dropForeignKey(
            $setup->getTable('os_barcode'),
            $setup->getFkName('os_barcode', 'product_id', 'catalog_product_entity', 'entity_id')
        );
        $setup->getConnection()->dropForeignKey(
            $setup->getTable('os_barcode'),
            $setup->getFkName('os_barcode', 'product_sku', 'catalog_product_entity', 'sku')
        );

        // add foreign key
        $fks = [];
        $fks[] = [
            'fk_name' => $setup->getFkName(
                'os_barcode',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'table_name' => $setup->getTable('os_barcode'),
            'column_name' => 'product_id',
            'ref_table_name' => $setup->getTable('catalog_product_entity'),
            'ref_column_name' => 'entity_id',
            'on_delete' => \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            'on_update' => \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        ];
        $fks[] = [
            'fk_name' => $setup->getFkName(
                'os_barcode',
                'product_sku',
                'catalog_product_entity',
                'sku'
            ),
            'table_name' => $setup->getTable('os_barcode'),
            'column_name' => 'product_sku',
            'ref_table_name' => $setup->getTable('catalog_product_entity'),
            'ref_column_name' => 'sku',
            'on_delete' => \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            'on_update' => \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        ];

        foreach ($fks as $fk) {
            $query = sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)',
                $fk['table_name'],
                $fk['fk_name'],
                $fk['column_name'],
                $fk['ref_table_name'],
                $fk['ref_column_name']
            );
            if ($fk['on_delete'] !== null) {
                $query .= ' ON DELETE ' . strtoupper($fk['on_delete']);
            }
            if ($fk['on_update'] !== null) {
                $query .= ' ON UPDATE ' . strtoupper($fk['on_update']);
            }
            $setup->getConnection()->query($query);
            $setup->getConnection()->resetDdlCache($fk['table_name']);
        }
    }
}
