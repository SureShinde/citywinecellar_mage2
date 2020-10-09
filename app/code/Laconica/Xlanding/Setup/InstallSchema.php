<?php

namespace Laconica\Xlanding\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->upgradeLandingTable($setup);
        $setup->endSetup();
    }

    /**
     * Add old_id column to xlanding_page table, need for import process
     * @param SchemaSetupInterface $setup
     */
    private function upgradeLandingTable(SchemaSetupInterface $setup)
    {
        if (!$setup->getConnection()->isTableExists('amasty_xlanding_page')) {
            return;
        }
        $setup->getConnection()->addColumn(
            'amasty_xlanding_page',
            'old_id',
            [
                'type' => Table::TYPE_INTEGER,
                'default' => 0,
                'nullable' => true,
                'length' => 11,
                'comment' => 'Old ID'
            ]
        );

    }
}