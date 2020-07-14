<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Setup\UpgradeSchema;

use Amasty\Blog\Api\Data\VoteInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class CreateHelpfulTable
 */
class CreateHelpfulTable
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addTable($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addTable(SchemaSetupInterface $setup)
    {
        $tableVote = $setup->getConnection()->newTable(
            $setup->getTable(VoteInterface::MAIN_TABLE)
        )->addColumn(
            VoteInterface::VOTE_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Vote Id'
        )->addColumn(
            VoteInterface::POST_ID,
            Table::TYPE_INTEGER,
            null,
            ['default' => 0, 'nullable' => false],
            'Post table id'
        )->addColumn(
            VoteInterface::TYPE,
            Table::TYPE_SMALLINT,
            null,
            [],
            'type'
        )->addColumn(
            VoteInterface::IP,
            Table::TYPE_TEXT,
            256,
            [],
            'ip'
        )->setComment('Blog post vote table');
        $setup->getConnection()->createTable($tableVote);
    }
}
