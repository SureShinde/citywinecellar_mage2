<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\Rewards\Model\ConstantRegistryInterface as Constant;

class UpgradeDataTo170
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->setNotificationAttributes($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function setNotificationAttributes(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            'customer',
            Constant::NOTIFICATION_EARNING,
            [
                'type' => 'int',
                'visible' => 0,
                'required' => false,
                'visible_on_front' => 1,
                'is_user_defined' => 0,
                'is_system' => 1,
                'is_hidden' => 1,
                'label' => ''
            ]
        );

        $eavSetup->addAttribute(
            'customer',
            Constant::NOTIFICATION_EXPIRE,
            [
                'type' => 'int',
                'visible' => 0,
                'required' => false,
                'visible_on_front' => 1,
                'is_user_defined' => 0,
                'is_system' => 1,
                'is_hidden' => 1,
                'label' => ''
            ]
        );
    }
}
