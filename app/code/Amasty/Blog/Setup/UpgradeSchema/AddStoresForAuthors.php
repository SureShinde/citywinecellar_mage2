<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup\UpgradeSchema;

use Amasty\Blog\Api\Data\AuthorInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\Blog\Model\ResourceModel\Author;
use Magento\Framework\DB\Adapter\AdapterInterface;

class AddStoresForAuthors
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
        $storeTable = $setup->getTable(Author::STORE_TABLE_NAME);
        $table = $setup->getTable(Author::TABLE_NAME);
        $connection = $setup->getConnection();
        try {
            $this->createStoreTable($setup, $connection, $storeTable, $table);
            $this->moveDataInStoreTable($connection, $table, $storeTable);
            $this->dropOldColumns($connection, $table);
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
                AuthorInterface::AUTHOR_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Author Id'
            )->addColumn(
                AuthorInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Store Id'
            )->addColumn(
                AuthorInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['default' => null, 'unique' => true],
                'Name'
            )->addColumn(
                AuthorInterface::META_TITLE,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Title'
            )->addColumn(
                AuthorInterface::META_TAGS,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Tags'
            )->addColumn(
                AuthorInterface::META_DESCRIPTION,
                Table::TYPE_TEXT,
                null,
                [],
                'Meta Description'
            )->addForeignKey(
                $setup->getFkName(
                    $storeTable,
                    AuthorInterface::AUTHOR_ID,
                    $table,
                    AuthorInterface::AUTHOR_ID
                ),
                AuthorInterface::AUTHOR_ID,
                $table,
                AuthorInterface::AUTHOR_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $storeTable,
                    AuthorInterface::STORE_ID,
                    $magentoStoreTable,
                    AuthorInterface::STORE_ID
                ),
                AuthorInterface::STORE_ID,
                $magentoStoreTable,
                AuthorInterface::STORE_ID,
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
            ->from(['authors' => $table])
            ->reset(Select::COLUMNS)
            ->columns([
                AuthorInterface::AUTHOR_ID,
                AuthorInterface::NAME,
                AuthorInterface::META_TITLE,
                AuthorInterface::META_DESCRIPTION,
                AuthorInterface::META_TAGS,
            ]);

        $authors = $connection->fetchAll($select);
        if ($authors) {
            $connection->insertMultiple($storeTable, $authors);
        }
    }

    /**
     * @param AdapterInterface $connection
     * @param string $table
     */
    private function dropOldColumns(AdapterInterface $connection, $table)
    {
        $connection->dropColumn($table, AuthorInterface::NAME);
        $connection->dropColumn($table, AuthorInterface::META_TITLE);
        $connection->dropColumn($table, AuthorInterface::META_DESCRIPTION);
        $connection->dropColumn($table, AuthorInterface::META_TAGS);
        $connection->dropColumn($table, 'google_profile');
    }
}
