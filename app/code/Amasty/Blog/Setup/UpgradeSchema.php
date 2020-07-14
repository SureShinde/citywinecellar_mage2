<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup;

use Amasty\Blog\Api\Data\CategoryInterface;
use Amasty\Blog\Setup\UpgradeSchema\CreateRelatedPostField;
use Amasty\Blog\Setup\UpgradeSchema\CreateHelpfulTable;
use Amasty\Blog\Setup\UpgradeSchema\IsFeaturedField;
use Amasty\Blog\Setup\UpgradeSchema\AddStoresForCategories;
use Amasty\Blog\Setup\UpgradeSchema\AddStoresForAuthors;
use Amasty\Blog\Setup\UpgradeSchema\AddStoresForTags;
use Amasty\Blog\Setup\UpgradeSchema\ModifyDefaultDate;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Api\Data\TagInterface;
use Amasty\Blog\Api\Data\AuthorInterface;
use Amasty\Blog\Model\ResourceModel\Posts as PostResource;
use Amasty\Blog\Model\ResourceModel\Tag as TagResource;
use Amasty\Blog\Model\ResourceModel\Categories as CategoriesResource;
use Amasty\Blog\Model\ResourceModel\Author as AuthorResource;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CreateRelatedPostField
     */
    private $createRelatedPostField;

    /**
     * @var CreateHelpfulTable
     */
    private $createHelpfulTable;

    /**
     * @var IsFeaturedField
     */
    private $createIsFeaturedField;

    /**
     * @var AddStoresForCategories
     */
    private $addStoresForCategories;

    /**
     * @var AddStoresForAuthors
     */
    private $addStoresForAuthors;

    /**
     * @var AddStoresForTags
     */
    private $addStoresForTags;

    /**
     * @var ModifyDefaultDate
     */
    private $modifyDefaultDate;

    public function __construct(
        CreateRelatedPostField $createRelatedPostField,
        IsFeaturedField $createIsFeaturedField,
        CreateHelpfulTable $createHelpfulTable,
        AddStoresForCategories $addStoresForCategories,
        AddStoresForAuthors $addStoresForAuthors,
        AddStoresForTags $addStoresForTags,
        ModifyDefaultDate $modifyDefaultDate
    ) {
        $this->createRelatedPostField = $createRelatedPostField;
        $this->createIsFeaturedField = $createIsFeaturedField;
        $this->createHelpfulTable = $createHelpfulTable;
        $this->addStoresForCategories = $addStoresForCategories;
        $this->addStoresForAuthors = $addStoresForAuthors;
        $this->addStoresForTags = $addStoresForTags;
        $this->modifyDefaultDate = $modifyDefaultDate;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.2.3', '<')) {
            $this->addIndexFields($setup);
        }

        if (version_compare($context->getVersion(), '1.3.2', '<')) {
            $this->addThumbnailAltColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.7.1', '<')) {
            $this->addCategoryTreeColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.7.2', '<')) {
            $this->createAuthorTable($setup);
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->createRelatedPostField->execute($setup);
        }

        if (version_compare($context->getVersion(), '2.4.0', '<')) {
            $this->createHelpfulTable->execute($setup);
            $this->createIsFeaturedField->execute($setup);
        }

        if (version_compare($context->getVersion(), '2.5.0', '<')) {
            $this->addStoresForCategories->execute($setup);
            $this->addStoresForAuthors->execute($setup);
            $this->addStoresForTags->execute($setup);
        }

        if (version_compare($context->getVersion(), '2.5.1', '<')) {
            $this->modifyDefaultDate->execute($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addIndexFields(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $connection = $installer->getConnection();
        $connection->addIndex(
            $installer->getTable(PostResource::TABLE_NAME),
            $setup->getIdxName(
                $installer->getTable(PostResource::TABLE_NAME),
                [PostInterface::TITLE, PostInterface::SHORT_CONTENT, PostInterface::FULL_CONTENT],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [PostInterface::TITLE, PostInterface::SHORT_CONTENT, PostInterface::FULL_CONTENT],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );

        $connection->addIndex(
            $installer->getTable('amasty_blog_categories'),
            $setup->getIdxName(
                $installer->getTable('amasty_blog_categories'),
                ['name'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name'],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );

        $connection->addIndex(
            $installer->getTable(TagResource::TABLE_NAME),
            $setup->getIdxName(
                $installer->getTable(TagResource::TABLE_NAME),
                [TagInterface::NAME],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [TagInterface::NAME],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addThumbnailAltColumns(SchemaSetupInterface $setup)
    {
        $postTable = $setup->getTable(PostResource::TABLE_NAME);
        $setup->getConnection()->addColumn(
            $postTable,
            PostInterface::POST_THUMBNAIL_ALT,
            [
                'type'     => Table::TYPE_TEXT,
                'default'  => '',
                'nullable' => false,
                'length'   => 255,
                'comment'  => 'Thumbnail Post Alt'
            ]
        );

        $setup->getConnection()->addColumn(
            $postTable,
            PostInterface::LIST_THUMBNAIL_ALT,
            [
                'type'     => Table::TYPE_TEXT,
                'default'  => '',
                'nullable' => false,
                'length'   => 255,
                'comment'  => 'Thumbnail Post Alt'
            ]
        );
    }

    private function addCategoryTreeColumns(SchemaSetupInterface $setup)
    {
        $categoriesTable = $setup->getTable(CategoriesResource::TABLE_NAME);
        $setup->getConnection()->addColumn(
            $categoriesTable,
            CategoryInterface::PARENT_ID,
            [
                'type'     => Table::TYPE_INTEGER,
                'default'  => 0,
                'nullable' => true,
                'length'   => 11,
                'comment'  => 'Parent Id'
            ]
        );

        $setup->getConnection()->addColumn(
            $categoriesTable,
            CategoryInterface::PATH,
            [
                'type'     => Table::TYPE_TEXT,
                'default'  => '',
                'nullable' => false,
                'length'   => 255,
                'comment'  => 'path'
            ]
        );

        $setup->getConnection()->addColumn(
            $categoriesTable,
            CategoryInterface::LEVEL,
            [
                'type'     => Table::TYPE_INTEGER,
                'default'  => 1,
                'nullable' => false,
                'length'   => 11,
                'comment'  => 'Level'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    private function createAuthorTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(AuthorResource::TABLE_NAME))
            ->addColumn(
                AuthorInterface::AUTHOR_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Author Id'
            )->addColumn(
                AuthorInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['default' => null, 'unique' => true],
                'Name'
            )->addColumn(
                AuthorInterface::URL_KEY,
                Table::TYPE_TEXT,
                255,
                ['default' => null, 'unique' => true],
                'Url Key'
            )->addColumn(
                AuthorInterface::FACEBOOK_PROFILE,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Facebook Profile'
            )->addColumn(
                AuthorInterface::TWITTER_PROFILE,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Twitter profile'
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
            );
        $setup->getConnection()->createTable($table);

        $setup->getConnection()->addColumn(
            $setup->getTable(PostResource::TABLE_NAME),
            PostInterface::AUTHOR_ID,
            [
                'type'     => Table::TYPE_INTEGER,
                'default'  => null,
                'nullable' => true,
                'length'   => 11,
                'comment'  => 'Author Id'
            ]
        );
    }
}
