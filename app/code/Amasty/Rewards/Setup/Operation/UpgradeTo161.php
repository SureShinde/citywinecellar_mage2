<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo161
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->changeQuoteRewardAmountToDecimal($setup);
        $this->changeRewardAmountToDecimal($setup);
        $this->changeRewardCustomerAmountToDecimal($setup);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function changeQuoteRewardAmountToDecimal(SchemaSetupInterface $installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable('amasty_rewards_quote'))) {
            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_rewards_quote'),
                'reward_points',
                'reward_points',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,2',
                    'nullable' => false,
                    'default' => '0.00'
                ]
            );
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function changeRewardAmountToDecimal(SchemaSetupInterface $installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable('amasty_rewards_rule'))) {
            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_rewards_rule'),
                'amount',
                'amount',
                [
                    'type'     => Table::TYPE_DECIMAL,
                    'length'   => '12,2',
                    'nullable' => false,
                    'default'  => '0.00'
                ]
            );
            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_rewards_rule'),
                'spent_amount',
                'spent_amount',
                [
                    'type'     => Table::TYPE_DECIMAL,
                    'length'   => '12,2',
                    'nullable' => false,
                    'default'  => '0.00'
                ]
            );
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function changeRewardCustomerAmountToDecimal(SchemaSetupInterface $installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable('amasty_rewards_rewards'))) {
            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_rewards_rewards'),
                'amount',
                'amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,2',
                    'nullable' => false,
                    'default' => '0.00'
                ]
            );
            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_rewards_rewards'),
                'points_left',
                'points_left',
                [
                    'type'     => Table::TYPE_DECIMAL,
                    'length'   => '12,2',
                    'nullable' => false,
                    'default'  => '0.00'
                ]
            );
        }
    }
}
