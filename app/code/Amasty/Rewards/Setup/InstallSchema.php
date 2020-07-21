<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Rewards\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * create amasty_rewards_history table
         */
        $table = $installer
            ->getConnection()
            ->newTable($installer->getTable('amasty_rewards_history'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'date',
                Table::TYPE_TIMESTAMP,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                []
            )
            ->addColumn(
                'action_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addIndex('id', 'id');

        $installer->getConnection()->createTable($table);

        /**
         * create amasty_rewards_quote table
         */
        $table = $installer
            ->getConnection()
            ->newTable($installer->getTable('amasty_rewards_quote'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'reward_points',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addIndex('id', 'id');

        $installer->getConnection()->createTable($table);

        /**
         * create amasty_rewards_rewards table
         */
        $table = $installer
            ->getConnection()
            ->newTable($installer->getTable('amasty_rewards_rewards'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'action_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
            )
            ->addColumn(
                'amount',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false]
            )
            ->addColumn(
                'comment',
                Table::TYPE_TEXT,
                null,
                ['default' => null, 'nullable' => false]
            )
            ->addColumn(
                'action',
                Table::TYPE_TEXT,
                null,
                ['default' => null, 'nullable' => false]
            )
            ->addColumn(
                'points_left',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addIndex('id', 'id');

        $installer->getConnection()->createTable($table);

        /**
         * create amasty_rewards_rule table
         */

        $table = $installer
            ->getConnection()
            ->newTable($installer->getTable('amasty_rewards_rule'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'is_active',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0]
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['default' => null, 'nullable' => false]
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                null,
                ['default' => null, 'nullable' => true]
            )
            ->addColumn(
                'action',
                Table::TYPE_TEXT,
                255,
                ['default' => null, 'nullable' => true]
            )
            ->addColumn(
                'amount',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false]
            )
            ->addColumn(
                'spent_amount',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amasty_rewards_rule_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('amasty_rewards_rule_label')
        )->addColumn(
            'label_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rule Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addIndex(
            $installer->getIdxName(
                'amasty_rewards_rule_label',
                ['rule_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['rule_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('amasty_rewards_rule_label', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('amasty_rewards_rule_label', 'rule_id', 'amasty_rewards_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('amasty_rewards_rule'),
            'rule_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('amasty_rewards_rule_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Rewards Rule Label'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amasty_rewards_rule_website'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('amasty_rewards_rule_website')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'website_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addIndex(
            $installer->getIdxName('amasty_rewards_rule_website', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('amasty_rewards_rule_website', 'rule_id', 'amasty_rewards_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('amasty_rewards_rule'),
            'rule_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('amasty_rewards_rule_website', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Rewards Rules To Websites Relations'
        );

        $installer->getConnection()->createTable($table);

        /**
         * create amasty_rewards_rule_customer_group table
         */
        $describe = $installer->getConnection()->describeTable($installer->getTable('customer_group'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('amasty_rewards_rule_customer_group')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'customer_group_id',
            $describe['customer_group_id']['DATA_TYPE'] == 'int' ? Table::TYPE_INTEGER : Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName('amasty_rewards_rule_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName('customer_group', 'rule_id', 'amasty_rewards_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('amasty_rewards_rule'),
            'rule_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'amasty_rewards_rule_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Amasty Rewards Rules To Customer Groups Relations'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
