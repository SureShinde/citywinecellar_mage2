<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo170
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addNumberOfDaysInactive($setup);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addNumberOfDaysInactive(SchemaSetupInterface $installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable('amasty_rewards_rule'))) {
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_rewards_rule'),
                'inactive_days',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Number of Days Inactive'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_rewards_rule'),
                'recurring',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Recurring'
                ]
            );
        }
    }
}
