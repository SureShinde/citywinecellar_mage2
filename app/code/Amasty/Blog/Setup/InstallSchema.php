<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Amasty\Blog\Api\Data\ViewInterface;
use Amasty\Blog\Api\Data\TagInterface;
use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Model\ResourceModel\View as ViewResource;
use Amasty\Blog\Model\ResourceModel\Tag as TagResource;
use Amasty\Blog\Model\ResourceModel\Posts as PostResource;

/**
 * Class
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createCategories($setup);
        $this->createCategoriesStores($setup);
        $this->createPosts($setup);
        $this->createTags($setup);
        $this->createPostsTag($setup);
        $this->createPostsStores($setup);
        $this->createPostsCategory($setup);
        $this->createComments($setup);
        $this->createViews($setup);
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createComments(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_blog_comments'))
            ->addColumn(
                'comment_id',
                Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Comment Id'
            )->addColumn(
                'post_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => false],
                'Post Id'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => false],
                'Store Id'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false, 'primary' => false],
                'Status'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'primary' => false, 'default' => null],
                'Customer Id'
            )->addColumn(
                'reply_to',
                Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'primary' => false, 'default' => null],
                'Reply To'
            )->addColumn(
                'message',
                Table::TYPE_TEXT,
                null,
                [],
                'Message'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Name'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Email'
            )->addColumn(
                'session_id',
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Session Id'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addForeignKey(
                $installer->getFkName(
                    'amasty_blog_comments',
                    'post_id',
                    PostResource::TABLE_NAME,
                    PostInterface::POST_ID
                ),
                'post_id',
                $installer->getTable(PostResource::TABLE_NAME),
                PostInterface::POST_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'amasty_blog_comments',
                    'reply_to',
                    'amasty_blog_comments',
                    'post_id'
                ),
                'reply_to',
                $installer->getTable('amasty_blog_comments'),
                'comment_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createCategories(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_blog_categories'))
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Category Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Url Key'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false, 'unsigned' => true],
                'Status'
            )->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                5,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Sort Order'
            )->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Title'
            )->addColumn(
                'meta_tags',
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Tags'
            )->addColumn(
                'meta_description',
                Table::TYPE_TEXT,
                null,
                [],
                'Meta Description'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createCategoriesStores(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_blog_categories_store'))
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Category Id'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Store Id'
            )->addForeignKey(
                $installer->getFkName(
                    'amasty_blog_categories_store',
                    'category_id',
                    'amasty_blog_categories',
                    'category_id'
                ),
                'category_id',
                $installer->getTable('amasty_blog_categories'),
                'category_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createPosts(SchemaSetupInterface $installer)
    {

        $table = $installer->getConnection()
            ->newTable($installer->getTable(PostResource::TABLE_NAME))
            ->addColumn(
                PostInterface::POST_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Subscription Id'
            )->addColumn(
                PostInterface::STATUS,
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false, 'unsigned' => true],
                'Status'
            )->addColumn(
                PostInterface::TITLE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Title'
            )->addColumn(
                PostInterface::URL_KEY,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Url Key'
            )->addColumn(
                PostInterface::SHORT_CONTENT,
                Table::TYPE_TEXT,
                null,
                [],
                'Short Content'
            )->addColumn(
                PostInterface::FULL_CONTENT,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Full Content'
            )->addColumn(
                PostInterface::META_TITLE,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Title'
            )->addColumn(
                PostInterface::META_TAGS,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Meta Tags'
            )->addColumn(
                PostInterface::META_DESCRIPTION,
                Table::TYPE_TEXT,
                null,
                [],
                'Meta Description'
            )->addColumn(
                PostInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                PostInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addColumn(
                PostInterface::PUBLISHED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Published At'
            )->addColumn(
                PostInterface::RECENTLY_COMMENTED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => '0000-00-00 00:00:00'],
                'Recently Commented At'
            )->addColumn(
                PostInterface::USER_DEFINE_PUBLISH,
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => 0],
                'User Define Publish'
            )->addColumn(
                PostInterface::NOTIFY_ON_ENABLE,
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => 0],
                'Notify On Enable'
            )->addColumn(
                PostInterface::DISPLAY_SHORT_CONTENT,
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => 1],
                'Display Short Content'
            )->addColumn(
                PostInterface::COMMENTS_ENABLED,
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => 0],
                'Comments Enabled'
            )->addColumn(
                PostInterface::VIEWS,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Views'
            )->addColumn(
                PostInterface::POST_THUMBNAIL,
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Post Thumbnail'
            )->addColumn(
                PostInterface::LIST_THUMBNAIL,
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'List Thumbnail'
            )->addColumn(
                PostInterface::THUMBNAIL_URL,
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Thumbnail Url'
            )->addColumn(
                PostInterface::GRID_CLASS,
                Table::TYPE_TEXT,
                2,
                ['default' => 'w1', 'nullable' => false],
                'Grid Class'
            )->addColumn(
                PostInterface::CANONICAL_URL,
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Canonical Url'
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createPostsCategory(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_blog_posts_category'))
            ->addColumn(
                'post_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Post Id'
            )->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Category Id'
            )->addForeignKey(
                $installer->getFkName(
                    'amasty_blog_posts_category',
                    'post_id',
                    PostResource::TABLE_NAME,
                    PostInterface::POST_ID
                ),
                'post_id',
                $installer->getTable(PostResource::TABLE_NAME),
                PostInterface::POST_ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'amasty_blog_posts_category',
                    'category_id',
                    'amasty_blog_categories',
                    'category_id'
                ),
                'category_id',
                $installer->getTable('amasty_blog_categories'),
                'category_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createPostsStores(SchemaSetupInterface $installer)
    {

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_blog_posts_store'))
            ->addColumn(
                'post_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Post Id'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Store Id'
            )->addForeignKey(
                $installer->getFkName(
                    'amasty_blog_posts_store',
                    'post_id',
                    'amasty_blog_posts',
                    'post_id'
                ),
                'post_id',
                $installer->getTable(PostResource::TABLE_NAME),
                PostInterface::POST_ID,
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createPostsTag(SchemaSetupInterface $installer)
    {

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_blog_posts_tag'))
            ->addColumn(
                'post_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Post Id'
            )->addColumn(
                'tag_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Tag Id'
            )->addForeignKey(
                $installer->getFkName(
                    'amasty_blog_posts_tag',
                    'post_id',
                    PostResource::TABLE_NAME,
                    PostInterface::POST_ID
                ),
                'post_id',
                $installer->getTable(PostResource::TABLE_NAME),
                PostInterface::POST_ID,
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @throws \Zend_Db_Exception
     */
    private function createTags(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(TagResource::TABLE_NAME))
            ->addColumn(
                TagInterface::TAG_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Tag Id'
            )->addColumn(
                TagInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['default' => null, 'unique' => true],
                'Name'
            )->addColumn(
                TagInterface::URL_KEY,
                Table::TYPE_TEXT,
                255,
                ['default' => null],
                'Url Key'
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
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createViews(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(ViewResource::TABLE_NAME)
            )->addColumn(
                ViewInterface::VIEW_ID,
                Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'View Id'
            )->addColumn(
                ViewInterface::POST_ID,
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Post Id'
            )->addColumn(
                ViewInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true],
                'Customer Id'
            )->addColumn(
                ViewInterface::SESSION_ID,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Session Id'
            )->addColumn(
                ViewInterface::REMOTE_ADDR,
                Table::TYPE_BIGINT,
                20,
                ['nullable' => false],
                'Remote Address'
            )->addColumn(
                ViewInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => false, 'unsigned' => true],
                'Store Id'
            )->addColumn(
                ViewInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                'Created At'
            )->addColumn(
                ViewInterface::REFERER_URL,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Referer Url'
            )->addForeignKey(
                $installer->getFkName(
                    ViewResource::TABLE_NAME,
                    ViewInterface::POST_ID,
                    PostResource::TABLE_NAME,
                    PostInterface::POST_ID
                ),
                ViewInterface::POST_ID,
                $installer->getTable(PostResource::TABLE_NAME),
                PostInterface::POST_ID,
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }
}
