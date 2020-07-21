<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Setup;

use Magento\Framework\Setup;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * @var \Amasty\Rewards\Model\Sample
     */
    protected $sample;

    /**
     * Installer constructor.
     *
     * @param \Amasty\Rewards\Model\Sample $sample
     */
    public function __construct(\Amasty\Rewards\Model\Sample $sample)
    {
        $this->sample = $sample;
    }

    /**
     * @inheritdoc
     */
    public function install()
    {
        $this->sample->install(['Amasty_Rewards::fixtures/reward_rules.csv']);
    }
}
