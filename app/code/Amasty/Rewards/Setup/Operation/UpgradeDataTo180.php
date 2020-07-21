<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup\Operation;

use Amasty\Rewards\Model\TemplateSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeDataTo180
{
    /**
     * @var TemplateSetup
     */
    private $templateSetup;

    public function __construct(
        TemplateSetup $templateSetup
    ) {
        $this->templateSetup = $templateSetup;
    }

    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->addEmailTemplate();
    }

    private function addEmailTemplate()
    {
        $this->templateSetup->createTemplate(
            'amrewards_notification_balance_earn_template_modern',
            'Amasty Rewards: Reward Points Earned Modern'
        );
        $this->templateSetup->createTemplate(
            'amrewards_notification_balance_earn_template',
            'Amasty Rewards: Reward Points Earned'
        );
    }
}
