<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Amasty\Rewards\Model\ResourceModel\Quote as RewardsQuoteResource;
use Magento\Framework\Event\ObserverInterface;
use Amasty\Rewards\Api;
use Amasty\Rewards\Helper\Data as RewardsHelper;

class OrderCancelObserver implements ObserverInterface
{
    /**
     * @var Api\RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var Api\Data\ExpirationArgumentsInterfaceFactory
     */
    private $expirationFactory;

    /**
     * @var RewardsQuoteResource
     */
    private $rewardQuoteResource;

    public function __construct(
        Api\RewardsProviderInterface $rewardsProvider,
        Api\Data\ExpirationArgumentsInterfaceFactory $expirationFactory,
        RewardsQuoteResource $rewardQuoteResource
    ) {
        $this->rewardsProvider = $rewardsProvider;
        $this->expirationFactory = $expirationFactory;
        $this->rewardQuoteResource = $rewardQuoteResource;
    }

    /**
     * Return reward points
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getCustomerId() && ($amount = $this->rewardQuoteResource->getUsedRewards($order->getQuoteId()))) {
            $comment = __('Order #%1 Canceled', $order->getIncrementId());
            /** @var Api\Data\ExpirationArgumentsInterface $expire */
            $expire = $this->expirationFactory->create();
            $this->rewardsProvider->addPoints(
                $amount,
                $order->getCustomerId(),
                RewardsHelper::CANCEL_ACTION,
                $comment,
                $expire
            );
        }

        return $this;
    }
}
