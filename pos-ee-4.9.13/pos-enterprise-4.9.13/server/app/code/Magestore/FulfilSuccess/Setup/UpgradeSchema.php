<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Cms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.2.1.1', '<')) {
            $this->removeForeignKey($setup);
        }
        if (version_compare($context->getVersion(), '1.2.1.2', '<')) {
            $this->addSourceCodeToFulfilTables($setup);
        }
        if (version_compare($context->getVersion(), '1.2.1.3', '<')) {
            $setup->getConnection()->dropForeignKey(
                $setup->getTable('os_fulfilsuccess_package'),
                $setup->getFkName(
                    'os_fulfilsuccess_package',
                    'track_id',
                    'sales_shipment_track',
                    'entity_id'
                )
            );
            $setup->getConnection()->addForeignKey(
                $setup->getFkName(
                    'os_fulfilsuccess_package',
                    'track_id',
                    'sales_shipment_track',
                    'entity_id'
                ),
                $setup->getTable('os_fulfilsuccess_package'),
                'track_id',
                $setup->getTable('sales_shipment_track'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            );
        }
    }

    /**
     * Remove unusual foreign key
     *
     * @param SchemaSetupInterface $setup
     */
    public function removeForeignKey(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->dropForeignKey(
            $setup->getTable('os_fulfilsuccess_pickrequest'),
            $setup->getFkName(
                'os_fulfilsuccess_pickrequest',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );

        $setup->getConnection()->dropIndex(
            $setup->getTable('os_fulfilsuccess_pickrequest'),
            $setup->getFkName(
                'os_fulfilsuccess_pickrequest',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );

        $setup->getConnection()->dropForeignKey(
            $setup->getTable('os_fulfilsuccess_packrequest'),
            $setup->getFkName(
                'os_fulfilsuccess_packrequest',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );

        $setup->getConnection()->dropIndex(
            $setup->getTable('os_fulfilsuccess_packrequest'),
            $setup->getFkName(
                'os_fulfilsuccess_packrequest',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );

        $setup->getConnection()->dropForeignKey(
            $setup->getTable('os_fulfilsuccess_package'),
            $setup->getFkName(
                'os_fulfilsuccess_package',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );

        $setup->getConnection()->dropIndex(
            $setup->getTable('os_fulfilsuccess_package'),
            $setup->getFkName(
                'os_fulfilsuccess_package',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );
    }

    /**
     * Add column source_code to fulfil table
     *
     * @param SchemaSetupInterface $setup
     */
    public function addSourceCodeToFulfilTables(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('os_fulfilsuccess_pickrequest'),
            'source_code',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Source Code'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('os_fulfilsuccess_packrequest'),
            'source_code',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Source Code'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('os_fulfilsuccess_package'),
            'source_code',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Source Code'
            ]
        );
    }
}
