<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Plugin\SalesRule\Model;

class Quote
{
    /**
     * @var \Amasty\Rewards\Model\Quote
     */
    protected $rewardQuote;

    public function __construct(\Amasty\Rewards\Model\Quote $rewardQuote)
    {
        $this->rewardQuote = $rewardQuote;
    }

    public function afterSave($subject)
    {
        $points = $subject->getData('amrewards_point');

        if ($points) {
            $this->rewardQuote->addReward(
                $subject->getEntityId(),
                $subject->getData('amrewards_point')
            );
        }

        return $subject;
    }
}
