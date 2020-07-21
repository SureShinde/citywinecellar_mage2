<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Amasty\Rewards\Model\ResourceModel\Expiration;
use Amasty\Rewards\Model\ResourceModel\Rewards;
use Amasty\Rewards\Model\TemplateSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeDataTo181
{
    /**
     * @var Rewards
     */
    private $rewardsResource;

    /**
     * @var TemplateSetup
     */
    private $templateSetup;

    public function __construct(
        Rewards $rewardsResource,
        TemplateSetup $templateSetup
    ) {
        $this->rewardsResource = $rewardsResource;
        $this->templateSetup = $templateSetup;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->migrateRewards($setup);
        $this->addEmailTemplate();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    private function migrateRewards(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->insertMultiple(
            $setup->getTable(Expiration::TABLE_NAME),
            $this->rewardsResource->getRewards()
        );
    }

    private function addEmailTemplate()
    {
        $this->templateSetup->createTemplate(
            'amrewards_notification_points_expiring_template',
            'Amasty Rewards: Reward Points Expiring'
        );
        $this->templateSetup->createTemplate(
            'amrewards_notification_points_expiring_template_modern',
            'Amasty Rewards: Reward Points Expiring Modern'
        );
    }
}
