<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;
use Amasty\Rewards\Model\Config as ConfigProvider;
use Amasty\Rewards\Model\ResourceModel\Quote as RewardsQuoteResource;
use Magento\Sales\Model\Order;

class OrderLoadAfter implements ObserverInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RewardsQuoteResource
     */
    private $rewardQuoteResource;

    public function __construct(ConfigProvider $configProvider, RewardsQuoteResource $rewardQuoteResource)
    {
        $this->configProvider = $configProvider;
        $this->rewardQuoteResource = $rewardQuoteResource;
    }

    /**
     * Set forced can creditmemo flag if order payed fully by reward points
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        if (!$this->configProvider->isEnabled() || $order->canUnhold() || $order->isCanceled()
            || $order->getState() === Order::STATE_CLOSED
            || $order->getTotalPaid() > 0
        ) {
            return $this;
        }

        if ($this->rewardQuoteResource->getUsedRewards($order->getQuoteId()) > $order->getTotalRefunded()) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }
}
