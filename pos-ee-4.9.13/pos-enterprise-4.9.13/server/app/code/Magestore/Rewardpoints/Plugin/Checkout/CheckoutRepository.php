<?php
namespace Magestore\Rewardpoints\Plugin\Checkout;
/**
 * Class CheckoutRepository
 * @package Magestore\Rewardpoints\Plugin\Checkout
 */
class CheckoutRepository {
    /**
     * @param CheckoutRepository $subject
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @param $create_shipment
     * @param $create_invoice
     */
    public function beforePlaceOrder(
        \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject,
        \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order,
        $create_shipment,
        $create_invoice
    ) {
        $this->populateExtensionData($order);
    }

    /**
     * @param \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     */
    public function beforeAddItemToOrder(
        \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject,
        $order,
        $items
    ) {
        foreach ($items as &$item){
            $extensionAttributes = $item->getExtensionAttributes();
            $item->setRewardpointsSpent($extensionAttributes->getRewardpointsSpent());
            $item->setRewardpointsEarn($extensionAttributes->getRewardpointsEarn());
            $item->setRewardpointsBaseDiscount($extensionAttributes->getRewardpointsBaseDiscount());
            $item->setRewardpointsDiscount($extensionAttributes->getRewardpointsDiscount());
        }
    }

    /**
     * @param \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     */
    public function beforeHoldOrder(
        \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject,
        \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
    ) {
        $this->populateExtensionData($order);
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @return mixed
     */
    public function populateExtensionData($order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        $order->setRewardpointsSpent($extensionAttributes->getRewardpointsSpent());
        $order->setRewardpointsEarn($extensionAttributes->getRewardpointsEarn());
        $order->setRewardpointsBaseDiscount($extensionAttributes->getRewardpointsBaseDiscount());
        $order->setRewardpointsDiscount($extensionAttributes->getRewardpointsDiscount());
        $order->setRewardpointsBaseAmount($extensionAttributes->getRewardpointsBaseAmount());
        $order->setRewardpointsAmount($extensionAttributes->getRewardpointsAmount());
        $order->setRewardpointsBaseDiscountForShipping($extensionAttributes->getRewardpointsBaseDiscountForShipping());
        $order->setRewardpointsDiscountForShipping($extensionAttributes->getRewardpointsDiscountForShipping());
        $order->setCreditmemoRewardpointsEarn($extensionAttributes->getCreditmemoRewardpointsEarn());
        $order->setCreditmemoRewardpointsDiscount($extensionAttributes->getCreditmemoRewardpointsDiscount());
        $order->setCreditmemoRewardpointsBaseDiscount($extensionAttributes->getCreditmemoRewardpointsBaseDiscount());
        return $order;
    }
}