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
 * Class CreateRelatedPostField
 */
class CreateRelatedPostField
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
    private function addFields($setup)
    {
        $table = $setup->getTable(PostResource::TABLE_NAME);
        $setup->getConnection()->addColumn(
            $table,
            PostInterface::RELATED_POST_IDS,
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => false,
                'default'  => false,
                'size'     => null,
                'comment'  => 'Related Post Ids'
            ]
        );
    }
}
