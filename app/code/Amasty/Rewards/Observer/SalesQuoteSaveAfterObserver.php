<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteSaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Amasty\Rewards\Model\Quote
     */
    protected $rewardsQuote;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Amasty\Rewards\Model\Quote $rewardsQuote
    ) {
        $this->rewardsQuote = $rewardsQuote;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getCart()->getQuote();
        if ($quote->getData('amrewards_point')) {
            $this->rewardsQuote->addReward(
                $quote->getId(),
                $quote->getData('amrewards_point')
            );
        }
    }
}
