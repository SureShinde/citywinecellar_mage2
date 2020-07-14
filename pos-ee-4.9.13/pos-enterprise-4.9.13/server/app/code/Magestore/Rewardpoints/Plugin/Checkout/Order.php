<?php
namespace Magestore\Rewardpoints\Plugin\Checkout;
/**
 * Class Order
 * @package Magestore\Rewardpoints\Plugin\Checkout
 */
class Order {
    /**
     * @var $orderExtensionFactory
     */
    protected $orderExtensionFactory;


    /**
     * @var \Magestore\Webpos\Api\Sales\OrderRepositoryInterface
     */
    protected $orderRepository;


    /**
     * Order constructor.
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterfaceFactory $orderExtensionFactory
     * @param \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterfaceFactory $orderExtensionFactory,
        \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface|null $orderExtension
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface
     */
    public function afterGetExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order,
        \Magestore\Webpos\Api\Data\Checkout\OrderExtensionInterface $orderExtension = null
    ) {
        if ($orderExtension === null) {
            $orderExtension = $this->orderExtensionFactory->create();
        }

        $rewardpointsSpent = $orderExtension->getRewardpointsSpent();
        if ($rewardpointsSpent === null) {
            $rewardpointsSpent = $order->getRewardpointsSpent();
            $orderExtension->setRewardpointsSpent($rewardpointsSpent);
        }

        $rewardpointsEarn = $orderExtension->getRewardpointsEarn();
        if ($rewardpointsEarn === null) {
            $rewardpointsEarn = $order->getRewardpointsEarn();
            $orderExtension->setRewardpointsEarn($rewardpointsEarn);
        }

        $rewardpointsBaseDiscount = $orderExtension->getRewardpointsBaseDiscount();
        if ($rewardpointsBaseDiscount === null) {
            $rewardpointsBaseDiscount = $order->getRewardpointsBaseDiscount();
            $orderExtension->setRewardpointsBaseDiscount($rewardpointsBaseDiscount);
        }

        $rewardpointsDiscount = $orderExtension->getRewardpointsDiscount();
        if ($rewardpointsDiscount === null) {
            $rewardpointsDiscount = $order->getRewardpointsDiscount();
            $orderExtension->setRewardpointsDiscount($rewardpointsDiscount);
        }

        $rewardpointsBaseAmount = $orderExtension->getRewardpointsBaseAmount();
        if ($rewardpointsBaseAmount === null) {
            $rewardpointsBaseAmount = $order->getRewardpointsBaseAmount();
            $orderExtension->setRewardpointsBaseAmount($rewardpointsBaseAmount);
        }

        $rewardpointsAmount = $orderExtension->getRewardpointsAmount();
        if ($rewardpointsAmount === null) {
            $rewardpointsAmount = $order->getRewardpointsAmount();
            $orderExtension->setRewardpointsAmount($rewardpointsAmount);
        }

        $rewardpointsBaseDiscountForShipping = $orderExtension->getRewardpointsBaseDiscountForShipping();
        if ($rewardpointsBaseDiscountForShipping === null) {
            $rewardpointsBaseDiscountForShipping = $order->getRewardpointsBaseDiscountForShipping();
            $orderExtension->setRewardpointsBaseDiscountForShipping($rewardpointsBaseDiscountForShipping);
        }

        $rewardpointsDiscountForShipping = $orderExtension->getRewardpointsDiscountForShipping();
        if ($rewardpointsDiscountForShipping === null) {
            $rewardpointsDiscountForShipping = $order->getRewardpointsDiscountForShipping();
            $orderExtension->setRewardpointsDiscountForShipping($rewardpointsDiscountForShipping);
        }

        $creditmemoRewardpointsEarn = $orderExtension->getCreditmemoRewardpointsEarn();
        if ($creditmemoRewardpointsEarn === null) {
            $creditmemoRewardpointsEarn = $order->getCreditmemoRewardpointsEarn();
            $orderExtension->setCreditmemoRewardpointsEarn($creditmemoRewardpointsEarn);
        }

        $creditmemoRewardpointsDiscount = $orderExtension->getCreditmemoRewardpointsDiscount();
        if ($creditmemoRewardpointsDiscount === null) {
            $creditmemoRewardpointsDiscount = $order->getCreditmemoRewardpointsDiscount();
            $orderExtension->setCreditmemoRewardpointsDiscount($creditmemoRewardpointsDiscount);
        }

        $creditmemoRewardpointsBaseDiscount = $orderExtension->getCreditmemoRewardpointsBaseDiscount();
        if ($creditmemoRewardpointsBaseDiscount === null) {
            $creditmemoRewardpointsBaseDiscount = $order->getCreditmemoRewardpointsBaseDiscount();
            $orderExtension->setCreditmemoRewardpointsBaseDiscount($creditmemoRewardpointsBaseDiscount);
        }

        $order->setExtensionAttributes($orderExtension);
        return $orderExtension;
    }
}