<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo160
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('amasty_rewards_rule');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'promo_skus',
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'default'  => null,
                'comment'  => 'Promo SKUs'
            ]
        );
    }
}
