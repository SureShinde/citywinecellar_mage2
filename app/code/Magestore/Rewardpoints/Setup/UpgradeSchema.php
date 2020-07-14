<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Rewardpoints\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const REWARDPOINTS_RATE_TABLE = 'rewardpoints_rate';
    const QUOTE_TABLE = 'quote';
    const QUOTE_ITEM_TABLE = 'quote_item';
    const QUOTE_ADDRESS_TABLE = 'quote_address';
    const ORDER_TABLE = 'sales_order';
    const ORDER_ITEM_TABLE = 'sales_order_item';
    const REWARDPOINTS_CUSTOMER = 'rewardpoints_customer';
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeSchema constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable(self::REWARDPOINTS_RATE_TABLE),
                'money',
                'money',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'comment' => 'Money'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $setup->getConnection()->dropForeignKey(
                $setup->getTable(self::REWARDPOINTS_RATE_TABLE),
                $setup->getFkName(
                    self::REWARDPOINTS_RATE_TABLE,
                    'website_ids',
                    'store_website',
                    'website_id'
                )
            );

            $setup->getConnection()->dropIndex(
                $setup->getTable(self::REWARDPOINTS_RATE_TABLE),
                $setup->getFkName(
                    self::REWARDPOINTS_RATE_TABLE,
                    'website_ids',
                    'store_website',
                    'website_id'
                )
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable(self::REWARDPOINTS_RATE_TABLE),
                'website_ids',
                'website_ids',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Website Ids'
                ]
            );


            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'magestore_base_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'magestore_base_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'magestore_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'magestore_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'rewardpoints_spent')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'rewardpoints_spent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Spent'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'rewardpoints_earn')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'rewardpoints_earn',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Earn'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'rewardpoints_base_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'rewardpoints_base_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Base Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'rewardpoints_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'rewardpoints_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'rewardpoints_base_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'rewardpoints_base_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'rewardpoints_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'rewardpoints_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore  Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'rewardpoints_spent')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'rewardpoints_spent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => '11',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Spent'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_base_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'magestore_base_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'magestore_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'rewardpoints_base_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'rewardpoints_base_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Base Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'rewardpoints_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'rewardpoints_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'rewardpoints_base_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'rewardpoints_base_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'rewardpoints_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'rewardpoints_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore  Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_base_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'magestore_base_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                    'magestore_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore  Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ITEM_TABLE), 'rewardpoints_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ITEM_TABLE),
                    'rewardpoints_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ITEM_TABLE), 'magestore_base_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ITEM_TABLE),
                    'magestore_base_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ITEM_TABLE), 'magestore_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ITEM_TABLE),
                    'magestore_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ITEM_TABLE), 'rewardpoints_spent')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_ITEM_TABLE),
                    'rewardpoints_spent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => '11',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Rewardpoints Spent'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_base_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_TABLE),
                    'magestore_base_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_TABLE),
                    'magestore_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_base_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_TABLE),
                    'magestore_base_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_TABLE),
                    'magestore_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'rewardpoints_base_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_TABLE),
                    'rewardpoints_base_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'rewardpoints_discount_for_shipping')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_TABLE),
                    'rewardpoints_discount_for_shipping',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Discount For Shipping'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_ITEM_TABLE), 'magestore_base_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_ITEM_TABLE),
                    'magestore_base_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Base Discount'
                    ]
                );
            }
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_ITEM_TABLE), 'magestore_discount')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::ORDER_ITEM_TABLE),
                    'magestore_discount',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Magestore Discount'
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '1.2.0.3', '<')) {
            $setup->getConnection()->addIndex(
                $setup->getTable('rewardpoints_customer'),
                'REWARDPOINTS_CUSTOMER_IDX',
                ['customer_id']
            );
        }
        if (version_compare($context->getVersion(), '1.2.0.4', '<')) {
            if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'use_max_point')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::QUOTE_TABLE),
                    'use_max_point',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'unsigned' => true,
                        'default' => '0',
                        'comment' => 'Is use max point'
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '1.2.1.0', '<')) {
            if(!$setup->getConnection()->tableColumnExists($setup->getTable(self::REWARDPOINTS_CUSTOMER), 'updated_at')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable(self::REWARDPOINTS_CUSTOMER),
                    'updated_at',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        'length' => null,
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                        'comment' => 'Updated At'
                    ]
                );
            }
        }
        $setup->endSetup();
    }
}
