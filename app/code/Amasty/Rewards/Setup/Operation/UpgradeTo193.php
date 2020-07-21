<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Amasty\Rewards\Api\Data\HistoryInterface;
use Amasty\Rewards\Model\ResourceModel\History;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo193
{
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addParamsColumn($setup);
    }

    /**
     * Add column params to table
     *
     * @param SchemaSetupInterface $installer
     */
    private function addParamsColumn($installer)
    {
        if ($installer->getConnection()->isTableExists($installer->getTable(History::TABLE_NAME))) {
            $installer->getConnection()->addColumn(
                $installer->getTable(History::TABLE_NAME),
                HistoryInterface::PARAMS,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default'  => null,
                    'comment'  => 'Additional params for applied rule'
                ]
            );
        }
    }
}
