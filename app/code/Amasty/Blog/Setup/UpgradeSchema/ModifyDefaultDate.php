<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */



namespace Amasty\Blog\Setup\UpgradeSchema;

use Amasty\Blog\Api\Data\PostInterface;
use Amasty\Blog\Model\ResourceModel\Posts as PostResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class ModifyDefaultDate
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable(PostResource::TABLE_NAME);
        $connection->modifyColumn(
            $table,
            PostInterface::PUBLISHED_AT,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT
            ]
        );
        $connection->modifyColumn(
            $table,
            PostInterface::RECENTLY_COMMENTED_AT,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT
            ]
        );
    }
}
