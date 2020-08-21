<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Gift Card module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const QUOTE_TABLE = 'quote';
    const QUOTE_ITEM_TABLE = 'quote_item';
    const QUOTE_ADDRESS_TABLE = 'quote_address';
    const ORDER_TABLE = 'sales_order';
    const ORDER_ITEM_TABLE = 'sales_order_item';
    const GIFTCARD_TEMPLATE_TABLE = 'giftcard_template';

    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface
     */
    protected $giftTemplateIOService;

    /**
     * UpgradeSchema constructor.
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $giftTemplateIOService
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $giftTemplateIOService
    ) {
        $this->giftTemplateIOService = $giftTemplateIOService;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $setup->getConnection()->dropTable($setup->getTable('giftvoucher_sets'));
            $setup->getConnection()->addColumn(
                $setup->getTable('giftvoucher'),
                'used',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT
            );

            $table = $setup->getConnection()->newTable(
                $setup->getTable('giftvoucher_sets')
            )->addColumn(
                'set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Set Id'
            )->addColumn(
                'set_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                45,
                ['default' => ''],
                'Set Name'
            )->addColumn(
                'sets_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => '0'],
                'Set Qty'
            )->addIndex(
                $setup->getIdxName('giftvoucher_sets', ['set_id']),
                ['set_id']
            );
            $setup->getConnection()->createTable($table);

            $setup->getConnection()->addColumn(
                $setup->getTable('giftvoucher'),
                'set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER
            );
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            /* add created_at & updated_at to giftcard_template */
            $setup->getConnection()->addColumn(
                $setup->getTable('giftcard_template'),
                'created_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                    'comment' => 'Created At'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('giftcard_template'),
                'updated_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                    'comment' => 'Updated At'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_gift_codes',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_gift_codes_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes Discount'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_gift_codes_max_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes Max Discount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_base_shipping_hidden_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Shipping Hidden Tax Amount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_shipping_hidden_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Shipping Hidden Tax Amount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'base_giftvoucher_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Base Gift Voucher Discount For Shipping'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Discount For Shipping'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_base_hidden_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Hidden Tax Amount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_hidden_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Hidden Tax Amount'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'base_gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Base Gift Voucher Discount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Discount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'codes_base_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Base Discount String'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'codes_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Discount String'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'base_gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Base Gift Voucher Discount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Discount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'giftvoucher_base_hidden_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Hidden Tax Amount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'giftvoucher_hidden_tax_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Hidden Tax Amount'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'gift_voucher_gift_codes',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'gift_voucher_gift_codes_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes Discount'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'codes_base_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Base Discount String'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'codes_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Discount String'
                ]
            );
            $setup->getConnection()->modifyColumn(
                $setup->getTable('giftvoucher_history'),
                'created_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ]
            );

            $setup->getConnection()->modifyColumn(
                $setup->getTable('giftvoucher_customer_voucher'),
                'added_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ]
            );

            $setup->getConnection()->modifyColumn(
                $setup->getTable('giftcard_template'),
                'design_pattern',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface::DEFAULT_TEMPLATE_ID
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->updateRebuiltDiscount($setup);
            $this->updateGiftcardTemplate($setup);
        }

        if (version_compare($context->getVersion(), '2.2.0.6', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('giftvoucher_history'),
                'order_item_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'length' => 11,
                    'comment' => 'Order Item Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.0.7', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('giftvoucher_history'),
                'creditmemo_increment_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'length' => 11,
                    'comment' => 'Creditmemo Increment Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.0.8', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_item'),
                'giftcodes_applied',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '65536',
                    'comment' => 'Giftcode(s) applied'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'giftcodes_applied',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '65536',
                    'comment' => 'Giftcode(s) applied'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.2.0.9', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'giftcodes_applied_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '65536',
                    'comment' => 'Giftcode(s) applied discount for shipping'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftcodes_applied_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '65536',
                    'comment' => 'Giftcode(s) applied discount for shipping'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote_address'),
                'giftcodes_applied_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '65536',
                    'comment' => 'Giftcode(s) applied discount for shipping'
                ]
            );
        }

        $setup->endSetup();
    }

    /**
     * Update table
     *
     * @param SchemaSetupInterface $setup
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function updateRebuiltDiscount(SchemaSetupInterface $setup)
    {

        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_TABLE),
            'magestore_base_discount'
        )) {
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

        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'magestore_base_discount'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'magestore_discount'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'base_gift_voucher_discount'
        )) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'base_gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Base Gift Card Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'gift_voucher_discount'
        )) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Gift Card Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'magestore_base_discount_for_shipping'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'magestore_discount_for_shipping'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'base_giftvoucher_discount_for_shipping'
        )) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'base_giftvoucher_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Base Giftvoucher Discount For Shipping'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ADDRESS_TABLE),
            'giftvoucher_discount_for_shipping'
        )) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'giftvoucher_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Giftvoucher Discount For Shipping'
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ITEM_TABLE),
            'magestore_base_discount'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::QUOTE_ITEM_TABLE),
            'magestore_discount'
        )) {
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

        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::ORDER_TABLE),
            'magestore_base_discount'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::ORDER_TABLE),
            'magestore_base_discount_for_shipping'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::ORDER_TABLE),
            'magestore_discount_for_shipping'
        )) {
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

        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::ORDER_ITEM_TABLE),
            'magestore_base_discount'
        )) {
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
        if (!$setup->getConnection()->tableColumnExists(
            $setup->getTable(self::ORDER_ITEM_TABLE),
            'magestore_discount'
        )) {
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

    /**
     * Update gift card template affer modify design_pattern column
     *
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     */
    public function updateGiftcardTemplate(SchemaSetupInterface $setup)
    {
        $templates = $this->giftTemplateIOService->getAvailableTemplates();
        if (empty($templates)) {
            return $this;
        }
        $count = count($templates);
        $index = 0;
        $updateValue = "CASE ";
        while ($index < $count) {
            $updateValue .= "WHEN MOD(giftcard_template_id, $count) = $index THEN '$templates[$index]' ";
            $index++;
        }
        $updateValue .= "ELSE design_pattern END";
        $setup->getConnection()->update(
            $setup->getTable(self::GIFTCARD_TEMPLATE_TABLE),
            ['design_pattern' => new \Zend_Db_Expr($updateValue)],
            ['design_pattern NOT IN (?)' => $templates]
        );
        return $this;
    }
}
