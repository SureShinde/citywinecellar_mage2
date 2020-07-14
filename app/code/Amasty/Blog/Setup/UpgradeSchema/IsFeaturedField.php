<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup\UpgradeSchema;

use Amasty\Blog\Model\ResourceModel\Posts as PostResource;
use Amasty\Blog\Api\Data\PostInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class IsFeaturedField
 */
class IsFeaturedField
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addFields($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addFields(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(PostResource::TABLE_NAME);
        $setup->getConnection()->addColumn(
            $table,
            PostInterface::IS_FEATURED,
            [
                'type'     => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default'  => false,
                'comment'  => 'Is Featured'
            ]
        );
    }
}
