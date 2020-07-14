<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup\UpgradeSchema;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Model\ResourceModel\Categories;

class AddStoresForCategories
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable(Categories::TABLE_NAME);
        $storesTable = $setup->getTable(Categories::STORE_TABLE_NAME);

        $this->addColumnsToStoreTable($connection, $storesTable);
        try {
            $this->insertDataForDefaultStore($connection, $table, $storesTable);

            $statuses = $this->getCategoryStatusesNoDefaultStore($connection, $table, $storesTable);

            $this->insertStatusNoDefaultStoreCategories($connection, $statuses, $storesTable);
            $this->dropCategoriesColumns($connection, $table);
            $this->addIndex($connection, $setup);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param AdapterInterface $connection
     * @param string $storesTable
     */
    private function addColumnsToStoreTable(AdapterInterface $connection, $storesTable)
    {
        $connection->addColumn(
            $storesTable,
            CategoryInterface::NAME,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'comment' => 'Name'
            ]
        );
        $connection->addColumn(
            $storesTable,
            CategoryInterface::STATUS,
            [
                'type' => Table::TYPE_SMALLINT,
                'default' => null,
                'unsigned' => true,
                'comment' => 'Status'
            ]
        );
        $connection->addColumn(
            $storesTable,
            CategoryInterface::META_TITLE,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'length' => 255,
                'comment' => 'Meta title'
            ]
        );
        $connection->addColumn(
            $storesTable,
            CategoryInterface::META_DESCRIPTION,
            [
                'type' => Table::TYPE_TEXT,
                'comment' => 'Meta description'
            ]
        );
        $connection->addColumn(
            $storesTable,
            CategoryInterface::META_TAGS,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'length' => 255,
                'comment' => 'Meta tags'
            ]
        );
    }

    /**
     * @param AdapterInterface $connection
     * @param string $table
     * @param string $storesTable
     */
    private function insertDataForDefaultStore(AdapterInterface $connection, $table, $storesTable)
    {
        $select = $connection->select()
            ->from(['categories' => $table])
            ->reset(Select::COLUMNS)
            ->columns([
                CategoryInterface::CATEGORY_ID,
                CategoryInterface::NAME,
                CategoryInterface::STATUS,
                CategoryInterface::META_TITLE,
                CategoryInterface::META_DESCRIPTION,
                CategoryInterface::META_TAGS,
            ]);
        $categories = [];
        foreach ($connection->fetchAll($select) as $category) {
            $category[CategoryInterface::STORE_ID] = 0;
            $categories[] = $category;
        }

        if ($categories) {
            $connection->delete($storesTable, 'store_id=0');
            $connection->insertMultiple($storesTable, $categories);
        }
    }

    /**
     * @param AdapterInterface $connection
     * @param string $table
     * @param string $storesTable
     * @return array
     */
    private function getCategoryStatusesNoDefaultStore(AdapterInterface $connection, $table, $storesTable)
    {
        $select = $connection->select()
            ->from(['categories' => $table])
            ->join(['store' => $storesTable], 'store.category_id=categories.category_id')
            ->reset(Select::COLUMNS)
            ->columns([
                CategoryInterface::STATUS => 'categories.status',
                CategoryInterface::CATEGORY_ID => 'categories.category_id',
                CategoryInterface::STORE_ID => 'store.store_id',
            ]);

        $categories = $connection->fetchAll($select);

        $statuses = [];
        $defaultStore = $this->getDefaultStatusesOnDefaultStore($categories);
        foreach ($categories as $category) {
            $isStatusesEquals = $defaultStore[$category[CategoryInterface::CATEGORY_ID]]
                == $category[CategoryInterface::STATUS];
            if (isset($defaultStore[$category[CategoryInterface::CATEGORY_ID]]) && $isStatusesEquals) {
                $category[CategoryInterface::STATUS] = null;
            }
            $statuses[] = $category;
        }

        return $statuses;
    }

    /**
     * @param array $categories
     * @return array
     */
    private function getDefaultStatusesOnDefaultStore($categories)
    {
        $defaultStore = [];
        foreach ($categories as $key => $category) {
            if ($category[CategoryInterface::STORE_ID] == '0') {
                $defaultStore[$category[CategoryInterface::CATEGORY_ID]] = $category[CategoryInterface::STATUS];
                unset($categories[$key]);
            }
        }

        return $defaultStore;
    }

    /**
     * @param AdapterInterface $connection
     * @param array $statuses
     * @param string $storesTable
     */
    private function insertStatusNoDefaultStoreCategories(AdapterInterface $connection, $statuses, $storesTable)
    {
        if ($statuses) {
            $connection->delete($storesTable, 'store_id<>0');
            $connection->insertMultiple($storesTable, $statuses);
        }
    }

    /**
     * @param AdapterInterface $connection
     * @param string $table
     */
    private function dropCategoriesColumns(AdapterInterface $connection, $table)
    {
        $connection->dropColumn($table, CategoryInterface::NAME);
        $connection->dropColumn($table, CategoryInterface::STATUS);
        $connection->dropColumn($table, CategoryInterface::META_TITLE);
        $connection->dropColumn($table, CategoryInterface::META_DESCRIPTION);
        $connection->dropColumn($table, CategoryInterface::META_TAGS);
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $setup
     */
    private function addIndex(AdapterInterface $connection, SchemaSetupInterface $setup)
    {
        $connection->addIndex(
            $setup->getTable(Categories::STORE_TABLE_NAME),
            $setup->getIdxName(
                $setup->getTable(Categories::STORE_TABLE_NAME),
                [CategoryInterface::NAME],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [CategoryInterface::NAME],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );
    }
}
