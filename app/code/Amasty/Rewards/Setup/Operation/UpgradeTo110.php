<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo110
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        if ($setup->getConnection()->isTableExists($setup->getTable('amasty_rewards_history'))) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('amasty_rewards_history'),
                'date',
                'date',
                [
                    'TYPE'     => Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default'  => Table::TIMESTAMP_INIT
                ]
            );
        }
    }
}
