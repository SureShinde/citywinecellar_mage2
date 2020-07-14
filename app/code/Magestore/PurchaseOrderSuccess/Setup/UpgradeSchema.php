<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Cms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $purchaseOrderFactory;
    protected $purchaseOrderRepository;
    protected $state;

    function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory $purchaseOrderFactory,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magento\Framework\App\State $state
    ) {
        $this->purchaseOrderFactory = $purchaseOrderFactory;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addKeyPurchaseOrder($setup);
            $this->addTableReturn($setup);
            $this->addTableReturnItems($setup);
            $this->addTableReturnItemsTranferred($setup);
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->removeKey($setup);
        }

        if (version_compare($context->getVersion(), '1.3.0.3', '<')) {
            // change type of field warehouse id
            $setup->getConnection()->changeColumn(
                $setup->getTable('os_purchase_order_item_transferred'),
                'warehouse_id',
                'warehouse_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Warehouse Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.3.0.4', '<')) {
            // change type of field warehouse id
            $setup->getConnection()->changeColumn(
                $setup->getTable('os_return_order'),
                'warehouse_id',
                'warehouse_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Warehouse Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.3.0.5', '<')) {
            $this->removeFkToCatalogProductEntityTable($setup);
        }
    }

    /**
     *
     * @param SchemaSetupInterface $setup
     * @return \Magestore\InventorySuccess\Setup\UpgradeSchema
     */
    public function addKeyPurchaseOrder(SchemaSetupInterface $setup)
    {
        if (!$setup->getConnection()->tableColumnExists($setup->getTable('os_purchase_order'), 'purchase_key')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('os_purchase_order'),
                'purchase_key',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Purchase key'
                ]
            );
        }
        return $this;
    }


    public function removeKey(SchemaSetupInterface $setup){

        $setup->getConnection()->dropForeignKey(
            $setup->getTable('os_return_order'),
            $setup->getFkName(
                'os_return_order',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );

        $setup->getConnection()->dropIndex(
            $setup->getTable('os_return_order'),
            $setup->getFkName(
                'os_return_order',
                'warehouse_id',
                'os_warehouse',
                'warehouse_id'
            )
        );

    }
    public function addTableReturn(SchemaSetupInterface $setup) {
        $setup->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('os_return_order'));

        /**
         * create os_return_order table
         */
        $table  = $setup->getConnection()
            ->newTable($setup->getTable('os_return_order'))
            ->addColumn(
                'return_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Return Id'
            )->addColumn(
                'return_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Return Code'
            )->addColumn(
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Warehouse Id'
            )->addColumn(
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Supplier Id'
            )->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => 1],
                'Type'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['default' => 0],
                'Status'
            )->addColumn(
                'reason',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Reason'
            )->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Created By'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'User Id'
            )->addColumn(
                'total_qty_returned',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Total Qty Returned'
            )->addColumn(
                'total_qty_transferred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Total Qty Transferred'
            )->addColumn(
                'returned_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Return Date'
            )->addColumn(
                'canceled_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['default' => null],
                'Canceled Date'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addForeignKey(
                $setup->getFkName(
                    'os_return_order',
                    'warehouse_id',
                    'os_warehouse',
                    'warehouse_id'
                ),
                'warehouse_id',
                $setup->getTable('os_warehouse'),
                'warehouse_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'os_return_order',
                    'supplier_id',
                    'os_supplier',
                    'supplier_id'
                ),
                'supplier_id',
                $setup->getTable('os_supplier'),
                'supplier_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'os_return_order',
                    'user_id',
                    'admin_user',
                    'user_id'
                ),
                'user_id',
                $setup->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    public function addTableReturnItems(SchemaSetupInterface $setup) {
        $setup->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('os_return_order_item'));
        /**
         * create os_return_order_item table
         */
        $table  = $setup->getConnection()
            ->newTable($setup->getTable('os_return_order_item'))
            ->addColumn(
                'return_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Return Item Id'
            )->addColumn(
                'return_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Return Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Product Id'
            )->addColumn(
                'product_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product SKU'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product Name'
            )->addColumn(
                'product_supplier_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Product Supplier SKU'
            )->addColumn(
                'qty_returned',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Returned Qty'
            )->addColumn(
                'qty_transferred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Transferred Qty'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addIndex(
                $setup->getIdxName('os_return_order_item', 'return_id'),
                'return_id'
            )->addIndex(
                $setup->getIdxName('os_return_order_item', 'product_id'),
                'product_id'
            )->addForeignKey(
                $setup->getFkName(
                    'os_return_order_item',
                    'return_id',
                    'os_return_order',
                    'return_id'
                ),
                'return_id',
                $setup->getTable('os_return_order'),
                'return_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'os_return_order_item',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }

    public function addTableReturnItemsTranferred(SchemaSetupInterface $setup) {
        $installer = $setup;
        $installer->startSetup();
        $installer->getConnection()->dropTable($setup->getTable('os_return_order_item_transferred'));
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('os_return_order_item_transferred'))
            ->addColumn(
                'return_item_transferred_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Return Request Item Transferred Id'
            )->addColumn(
                'return_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Return Request Item Id'
            )->addColumn(
                'qty_transferred',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['default' => 0],
                'Transferred Qty'
//            )->addColumn(
//                'warehouse_id',
//                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
//                '12,4',
//                ['nullable' => false, 'unsigned' => true],
//                'Warehouse Id'
            )->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['default' => ''],
                'Created By'
            )->addColumn(
                'transferred_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => false],
                'Transferred At'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addIndex(
                $installer->getIdxName('os_return_order_item_transferred', 'return_item_id'),
                'return_item_id'
            )->addForeignKey(
                $installer->getFkName(
                    'os_return_order_item_transferred',
                    'return_item_id',
                    'os_return_order_item',
                    'return_item_id'
                ),
                'return_item_id',
                $installer->getTable('os_return_order_item'),
                'return_item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }

    /**
     * Remove foreign key reference to catalog_product_entity
     *
     * @param SchemaSetupInterface $setup
     * */
    protected function removeFkToCatalogProductEntityTable (SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('os_purchase_order_item'),
            $installer->getFkName(
                'os_purchase_order_item',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            )
        )->dropForeignKey(
            $installer->getTable('os_return_order_item'),
            $installer->getFkName(
                'os_return_order_item',
                'product_id',
                'catalog_product_entity',
                'entity_id')
        );
        $installer->endSetup();
    }
}
