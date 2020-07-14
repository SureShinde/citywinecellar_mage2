<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Setup;

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
        $tableAttribute = $installer->getConnection()
            ->newTable($installer->getTable('amasty_banner_attribute'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Rule Id'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Code'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_banner_rule',
                    'rule_id',
                    'amasty_banner_attribute',
                    'id'
                ),
                'rule_id',
                $installer->getTable('amasty_banner_rule'),
                'id',
                Table::ACTION_CASCADE
            );

        $tableRule = $installer->getConnection()
            ->newTable($installer->getTable('amasty_banner_rule'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'rule_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Rule Name'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Is Active'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Sort order'
            )
            ->addColumn(
                'from_date',
                Table::TYPE_DATETIME,
                null,
                ['default' => null],
                'From Date'
            )
            ->addColumn(
                'to_date',
                Table::TYPE_DATETIME,
                null,
                ['default' => null],
                'To Date'
            )
            ->addColumn(
                'stores',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Stores'
            )
            ->addColumn(
                'cust_groups',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Customer Groups'
            )
            ->addColumn(
                'banner_position',
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Banner Position'
            )
            ->addColumn(
                'banner_img',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Banner Image'
            )
            ->addColumn(
                'banner_link',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Banner Link'
            )
            ->addColumn(
                'banner_title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Banner Title'
            )
            ->addColumn(
                'cms_block',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'CMS Block'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                null,
                [],
                'Conditions Serialized'
            )
            ->addColumn(
                'cats',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Cats'
            )
            ->addColumn(
                'show_on_products',
                Table::TYPE_TEXT,
                null,
                [],
                'Show On Products'
            )
            ->addColumn(
                'banner_type',
                Table::TYPE_TEXT,
                16,
                ['nullable' => false, 'default' => 'image'],
                '0 - image, 1 - cms block, 2 - html page, 3 - products'
            )
            ->addColumn(
                'html_text',
                Table::TYPE_TEXT,
                null,
                [],
                'HTML Text'
            )
            ->addColumn(
                'show_products',
                Table::TYPE_INTEGER,
                4,
                ['default' => 0],
                'Show Products'
            )
            ->addColumn(
                'actions_serialized',
                Table::TYPE_TEXT,
                null,
                ['default' => null, 'nullable' => true]
            )
            ->addColumn(
                'show_on_search',
                Table::TYPE_TEXT,
                null,
                [],
                'Show On Search'
            );

        $tableRuleProducts = $installer->getConnection()
            ->newTable($installer->getTable('amasty_banner_rule_products'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Rule Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Rule Id'
            );

        $installer->getConnection()->createTable($tableRule);
        $installer->getConnection()->createTable($tableAttribute);
        $installer->getConnection()->createTable($tableRuleProducts);
        $installer->endSetup();
    }
}
