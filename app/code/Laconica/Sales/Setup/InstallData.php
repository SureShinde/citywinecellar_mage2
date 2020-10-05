<?php

namespace Laconica\Sales\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $connection->update($connection->getTableName('sales_order_grid'), ['pos_location_id' => 0], 'pos_location_id IS NULL');
        $setup->startSetup();
    }
}