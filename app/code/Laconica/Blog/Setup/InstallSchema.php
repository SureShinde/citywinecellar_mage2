<?php

namespace Laconica\Blog\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Amasty\Blog\Model\ResourceModel\Categories as CategoriesResource;
use Amasty\Blog\Model\ResourceModel\Posts as PostResource;
use Amasty\Blog\Model\ResourceModel\Tag as TagResource;
use Laconica\Blog\Helper\Config;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->upgradeCategoryTable($setup);
        $this->upgradePostTable($setup);
        $this->upgradeTagTable($setup);
        $setup->endSetup();
    }

    /**
     * Add old_id column to category, need for import process
     * @param SchemaSetupInterface $setup
     */
    private function upgradeCategoryTable(SchemaSetupInterface $setup)
    {
        $categoriesTable = $setup->getTable(CategoriesResource::TABLE_NAME);
        $setup->getConnection()->addColumn(
            $categoriesTable,
            Config::OLD_ID_COLUMN,
            [
                'type' => Table::TYPE_INTEGER,
                'default' => 0,
                'nullable' => true,
                'length' => 11,
                'comment' => str_replace('_', ' ', Config::OLD_ID_COLUMN)
            ]
        );
    }

    /**
     * Add old_id column to post, need for import process
     * @param SchemaSetupInterface $setup
     */
    private function upgradePostTable(SchemaSetupInterface $setup)
    {
        $postsTable = $setup->getTable(PostResource::TABLE_NAME);
        $setup->getConnection()->addColumn(
            $postsTable,
            Config::OLD_ID_COLUMN,
            [
                'type' => Table::TYPE_INTEGER,
                'default' => 0,
                'nullable' => true,
                'length' => 11,
                'comment' => str_replace('_', ' ', Config::OLD_ID_COLUMN)
            ]
        );
    }

    /**
     * Add old_id column to tag, need for import process
     * @param SchemaSetupInterface $setup
     */
    private function upgradeTagTable(SchemaSetupInterface $setup)
    {
        $tagsTable = $setup->getTable(TagResource::TABLE_NAME);
        $setup->getConnection()->addColumn(
            $tagsTable,
            Config::OLD_ID_COLUMN,
            [
                'type' => Table::TYPE_INTEGER,
                'default' => 0,
                'nullable' => true,
                'length' => 11,
                'comment' => str_replace('_', ' ', Config::OLD_ID_COLUMN)
            ]
        );
    }
}