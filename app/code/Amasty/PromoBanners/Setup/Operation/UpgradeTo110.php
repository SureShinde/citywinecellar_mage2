<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo110
{
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_banner_rule');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $tableName,
            'segments',
            [
                'nullable' => true,
                'type'     => Table::TYPE_TEXT,
                'comment' => 'Segments'
            ]
        );
    }
}
