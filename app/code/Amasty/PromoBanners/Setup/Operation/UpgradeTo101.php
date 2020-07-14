<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeTo101
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_banner_rule'),
            'after_n_product_row',
            [
                'type'     => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment'  => 'Show Banner After N Products Row'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_banner_rule'),
            'n_product_width',
            [
                'type'     => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment'  => 'N Product Width'
            ]
        );
    }
}
