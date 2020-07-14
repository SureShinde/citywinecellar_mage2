<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup\UpgradeSchema;

use Amasty\Blog\Api\Data\TagInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Amasty\Blog\Model\ResourceModel\Tag;

class AddStoresForTags
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
        $storeTable = $setup->getTable(Tag::STORE_TABLE_NAME);
        $table = $setup->getTable(Tag::TABLE_NAME);
        $connection = $setup->getConnection();
        try {
            $this->createStoreTable($setup, $connection, $storeTable, $table);
            $this->moveDataInStoreTable($connection, $table, $storeTable);
            $this->dropOldColumns($connection, $table);
            $this->addIndex($connection, $setup);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param AdapterInterface $connection
     * @param string $storeTable
     * @param string $table
     * @throws \Zend_Db_Exception
     */
    private function createStoreTable(SchemaSetupInterface $setup, AdapterInterface $connection, $storeTable, $table)
    {
        $magentoStoreTable = $setup->getTable('store');
        $table = $connection->newTable($storeTable)
            ->addColumn(
                TagInterface::TAG_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Tag Id'
            )->addColumn(
                TagInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Store Id'
            )->addColumn(
                TagInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['default' => null, 'unique' => true],
                'Name'
            )->addColumn(
                TagInterface::META_TITLE,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Title'
            )->addColumn(
                TagInterface::META_TAGS,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Tags'
            )->addColumn(
                TagInterface::META_DESCRIPTION,
                Table::TYPE_TEXT,
                null,
                [],
                'Meta Description'
            )->addForeignKey(
                $setup->getFkName(
                    $storeTable,
                    TagInterface::TAG_ID,
                    $table,
                    TagInterface::TAG_ID
                ),
                TagInterface::TAG_ID,
                $table,
                TagInterface::TAG_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $storeTable,
                    TagInterface::STORE_ID,
                    $magentoStoreTable,
                    TagInterface::STORE_ID
                ),
                TagInterface::STORE_ID,
                $magentoStoreTable,
                TagInterface::STORE_ID,
                Table::ACTION_CASCADE
            );

        $connection->createTable($table);
    }

    /**
     * @param AdapterInterface $connection
     * @param string $table
     * @param string $storeTable
     */
    private function moveDataInStoreTable(AdapterInterface $connection, $table, $storeTable)
    {
        $select = $connection->select()
            ->from(['tags' => $table])
            ->reset(Select::COLUMNS)
            ->columns([
                TagInterface::TAG_ID,
                TagInterface::NAME,
                TagInterface::META_TITLE,
                TagInterface::META_DESCRIPTION,
                TagInterface::META_TAGS,
            ]);

        $tags = $connection->fetchAll($select);
        if ($tags) {
            $connection->insertMultiple($storeTable, $tags);
        }
    }
    /**
     * @param AdapterInterface $connection
     * @param string $table
     */
    private function dropOldColumns(AdapterInterface $connection, $table)
    {
        $connection->dropColumn($table, TagInterface::NAME);
        $connection->dropColumn($table, TagInterface::META_TITLE);
        $connection->dropColumn($table, TagInterface::META_DESCRIPTION);
        $connection->dropColumn($table, TagInterface::META_TAGS);
    }

    /**
     * @param AdapterInterface $connection
     * @param SchemaSetupInterface $setup
     */
    private function addIndex(AdapterInterface $connection, SchemaSetupInterface $setup)
    {
        $connection->addIndex(
            $setup->getTable(Tag::STORE_TABLE_NAME),
            $setup->getIdxName(
                $setup->getTable(Tag::STORE_TABLE_NAME),
                [TagInterface::NAME],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [TagInterface::NAME],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );
    }
}
