<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */



namespace Amasty\Rewards\Setup\Operation;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;

class UpgradeDataTo1140
{
    /**
     * @var \Magento\Sales\Setup\SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $installer = $setup;

        $installer->startSetup();

        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $installer]);
        $salesSetup->addAttribute(Order::ENTITY, 'am_earn_reward_points', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'visible' => false,
            'nullable' => true
        ]);

        $installer->endSetup();
    }
}
