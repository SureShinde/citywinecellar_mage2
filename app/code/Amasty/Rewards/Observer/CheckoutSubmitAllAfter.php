<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Helper\Data;
use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var RewardsProviderInterface
     */
    private $rewardsProvider;

    public function __construct(
        RewardsProviderInterface $rewardsProvider
    ) {
        $this->rewardsProvider = $rewardsProvider;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $comment = $order ? __('Order #%1', $order->getRealOrderId()) : null;

        if (floatval($quote->getAmrewardsPoint()) > 0) {
            $this->rewardsProvider->deductPoints(
                $quote->getAmrewardsPoint(),
                $quote->getCustomerId(),
                Data::REWARDS_SPEND_ACTION,
                $comment
            );
        }
    }
}
