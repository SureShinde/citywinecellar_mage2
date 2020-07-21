<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Amasty\Rewards\Api\Data\ExpirationDateInterface;
use Amasty\Rewards\Api\Data\RewardsInterface;
use Amasty\Rewards\Api\Data\RuleInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo181
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addExpirationPeriod($setup);
        $this->createExpirationPeriod($setup);
        $this->addExpirationId($setup);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addExpirationPeriod(SchemaSetupInterface $installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable('amasty_rewards_rule'))) {
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_rewards_rule'),
                RuleInterface::EXPIRATION_BEHAVIOR,
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 0,
                    'comment'  => 'Expiration Behavior'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_rewards_rule'),
                RuleInterface::EXPIRATION_PERIOD,
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment'  => 'Expiration Period'
                ]
            );
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createExpirationPeriod(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(\Amasty\Rewards\Model\ResourceModel\Expiration::TABLE_NAME))
            ->addColumn(
                ExpirationDateInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                ExpirationDateInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                ExpirationDateInterface::DATE,
                Table::TYPE_DATE,
                ['nullable' => true],
                []
            )
            ->addColumn(
                ExpirationDateInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,2',
                ['unsigned' => false, 'nullable' => false]
            )
            ->addForeignKey(
                $installer->getFkName(
                    \Amasty\Rewards\Model\ResourceModel\Expiration::TABLE_NAME,
                    ExpirationDateInterface::CUSTOMER_ID,
                    'customer_entity',
                    'entity_id'
                ),
                ExpirationDateInterface::CUSTOMER_ID,
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function addExpirationId(SchemaSetupInterface $installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable('amasty_rewards_rewards'))) {
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_rewards_rewards'),
                RewardsInterface::EXPIRATION_ID,
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment'  => 'Expiration ID of record from expiration_date table'
                ]
            );
        }
    }
}
