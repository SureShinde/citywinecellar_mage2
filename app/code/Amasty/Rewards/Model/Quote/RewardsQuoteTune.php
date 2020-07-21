<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Model\Quote;

use Amasty\Rewards\Model\QuoteFactory;
use Amasty\Rewards\Model\ResourceModel\Quote;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;

class RewardsQuoteTune
{
    /**
     * @var QuoteFactory
     */
    private $rewardsQuoteFactory;

    /**
     * @var Quote
     */
    private $rewardQuoteResource;

    /**
     * @var CollectionFactory
     */
    private $quoteCollectFactory;

    public function __construct(
        QuoteFactory $rewardsQuoteFactory,
        Quote $rewardQuoteResource,
        CollectionFactory $quoteCollectFactory
    ) {
        $this->rewardsQuoteFactory = $rewardsQuoteFactory;
        $this->rewardQuoteResource = $rewardQuoteResource;
        $this->quoteCollectFactory = $quoteCollectFactory;
    }

    /**
     * @param \Amasty\Rewards\Api\Data\RewardsInterface $modelRewards
     * @param int $customerId
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(\Amasty\Rewards\Api\Data\RewardsInterface $modelRewards, $customerId)
    {
        if ($modelRewards->getAmount() < 0) {
            $quoteCollection = $this->quoteCollectFactory->create()->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('is_active', 1);
            /** @var \Magento\Quote\Model\Quote $quote */
            foreach ($quoteCollection->getItems() as $quote) {
                $rewardQuote = $this->rewardsQuoteFactory->create();
                $this->rewardQuoteResource->load($rewardQuote, $quote->getId(), 'quote_id');
                if ($rewardQuote->getId()) {
                    if ($modelRewards->getPointsLeft() < 0.00001) {
                        $this->rewardQuoteResource->delete($rewardQuote);
                    } elseif ($modelRewards->getPointsLeft() < $rewardQuote->getRewardPoints()) {
                        $rewardQuote->setRewardPoints($modelRewards->getPointsLeft());
                        $this->rewardQuoteResource->save($rewardQuote);
                    }
                }

            }
        }
    }
}
