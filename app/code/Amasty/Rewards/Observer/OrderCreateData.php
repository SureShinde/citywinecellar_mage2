<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderCreateData implements ObserverInterface
{
    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Quote
     */
    private $rewardQuoteResource;

    /**
     * @var \Amasty\Rewards\Model\QuoteFactory
     */
    private $rewardQuoteFactory;

    /**
     * @var \Amasty\Rewards\Api\CheckoutRewardsManagementInterface
     */
    private $rewardsManagement;

    /**
     * @var \Amasty\Rewards\Api\RewardsRepositoryInterface
     */
    private $rewardsRepository;

    public function __construct(
        \Amasty\Rewards\Model\ResourceModel\Quote $rewardQuoteResource,
        \Amasty\Rewards\Model\QuoteFactory $rewardQuoteFactory,
        \Amasty\Rewards\Api\CheckoutRewardsManagementInterface $rewardsManagement,
        \Amasty\Rewards\Api\RewardsRepositoryInterface $rewardsRepository
    ) {
        $this->rewardQuoteResource = $rewardQuoteResource;
        $this->rewardQuoteFactory = $rewardQuoteFactory;
        $this->rewardsManagement = $rewardsManagement;
        $this->rewardsRepository = $rewardsRepository;
    }

    /**
     * Apply reward point in admin order create
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();
        if (isset($request['amreward_amount']) && $quote->getCustomerId()) {
            $balance = $this->rewardsRepository->getCustomerRewardBalance($quote->getCustomerId());
            $amount = min($request['amreward_amount'], $balance);

            /** @var \Amasty\Rewards\Model\Quote $rewardQuote */
            $rewardQuote = $this->rewardQuoteFactory->create();
            $this->rewardQuoteResource->load($rewardQuote, $quote->getId(), 'quote_id');
            $this->rewardsManagement->collectCurrentTotals($quote, $amount);

            $rewardQuote->setQuoteId($quote->getId())
                ->setRewardPoints($quote->getData('amrewards_point'));

            if ($rewardQuote->getRewardPoints()) {
                $this->rewardQuoteResource->save($rewardQuote);
            } elseif ($rewardQuote->getId()) {
                $this->rewardQuoteResource->delete($rewardQuote);
            }
        }

        return $this;
    }
}
