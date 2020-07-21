<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin\Sales\Api;

use Amasty\Rewards\Api;
use Amasty\Rewards\Block\Adminhtml\Sales\Creditmemo as CreditmemoBlock;
use Amasty\Rewards\Helper\Data as RewardsHelper;
use Amasty\Rewards\Model\Repository\RewardsRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\OrderRepository;

class CreditmemoRepositoryInterfacePlugin
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
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var RewardsRepository
     */
    private $rewardsRepository;

    public function __construct(
        Api\RewardsProviderInterface $rewardsProvider,
        Api\Data\ExpirationArgumentsInterfaceFactory $expirationFactory,
        OrderRepository $orderRepository,
        RewardsRepository $rewardsRepository
    ) {
        $this->rewardsProvider = $rewardsProvider;
        $this->expirationFactory = $expirationFactory;
        $this->orderRepository = $orderRepository;
        $this->rewardsRepository = $rewardsRepository;
    }

    /**
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface|\Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return \Magento\Sales\Api\Data\CreditmemoInterface|\Magento\Sales\Model\Order\Creditmemo
     */
    public function afterSave(\Magento\Sales\Api\CreditmemoRepositoryInterface $subject, $creditmemo)
    {
        if ($amount = $creditmemo->getData(CreditmemoBlock::REFUND_KEY)) {
            $order = $creditmemo->getOrder();
            $comment = __('Refund #%1 for Order #%2', $creditmemo->getIncrementId(), $order->getIncrementId());
            /** @var Api\Data\ExpirationArgumentsInterface $expire */
            $expire = $this->expirationFactory->create();
            $this->rewardsProvider->addPoints(
                $amount,
                $order->getCustomerId(),
                RewardsHelper::REFUND_ACTION,
                $comment,
                $expire
            );
        }

        if ($amount = $creditmemo->getData(CreditmemoBlock::EARNED_POINTS_KEY)) {
            $order = $creditmemo->getOrder();
            $this->validateEarnedPoints($order, $amount);
            $comment = __('Deduct Points #%1 for Order #%2', $creditmemo->getIncrementId(), $order->getIncrementId());
            $customerBalance = $this->rewardsRepository->getCustomerRewardBalance($order->getCustomerId());
            if ($customerBalance < $amount) {
                $amount = $customerBalance;
            }
            $this->rewardsProvider->deductPoints(
                $amount,
                $order->getCustomerId(),
                RewardsHelper::REFUND_ACTION,
                $comment
            );
            $this->updateRewardPoints($order, $amount);
        }

        return $creditmemo;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param float $amount
     * @throws LocalizedException
     * @throws \InvalidArgumentException
     */
    private function validateEarnedPoints($order, $amount)
    {
        if ($amount <= 0) {
            throw new LocalizedException(__('You are trying to deduct negative or null amount of rewards point(s).'));
        }

        if (!$amount || !$order) {
            throw new \InvalidArgumentException('Required parameter is not set.');
        }

        $currentBalance = $order->getAmEarnRewardPoints();

        if ($amount > $currentBalance) {
            throw new LocalizedException(__('Too much point(s) used.'));
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param float $amount
     */
    private function updateRewardPoints($order, $amount)
    {
        $currentBalance = $order->getAmEarnRewardPoints();
        $order->setAmEarnRewardPoints($currentBalance - $amount);
        $this->orderRepository->save($order);
    }
}
