<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.1.0.3', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('webpos_authorization_role'),
                'description',
                'description',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => false,
                    'length'    => \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
                    'comment'   => 'description'
                )
            );
        }
        if (version_compare($context->getVersion(), '1.0.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('webpos_authorization_role'),
                'max_discount_percent',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'nullable' => true,
                    'comment' => 'Maximum discount percent'
                )
            );
        }
        $setup->endSetup();
    }
}