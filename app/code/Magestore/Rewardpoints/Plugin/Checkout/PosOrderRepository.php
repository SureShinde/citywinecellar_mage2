<?php

namespace Magestore\Rewardpoints\Plugin\Checkout;

/**
 * Class PosOrderRepository
 *
 * @package Magestore\Rewardpoints\Plugin\Checkout
 */
class PosOrderRepository
{
    /**
     * After modify place order params
     *
     * @param \Magestore\Webpos\Model\Checkout\PosOrderRepository $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterModifyPlaceOrderParams(
        \Magestore\Webpos\Model\Checkout\PosOrderRepository $subject,
        $result
    ) {
        $order = $result[0];

        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes) {
            $order->setRewardpointsSpent($extensionAttributes->getRewardpointsSpent());
            $order->setRewardpointsEarn($extensionAttributes->getRewardpointsEarn());
            $order->setRewardpointsBaseDiscount($extensionAttributes->getRewardpointsBaseDiscount());
            $order->setRewardpointsDiscount($extensionAttributes->getRewardpointsDiscount());
            $order->setRewardpointsBaseAmount($extensionAttributes->getRewardpointsBaseAmount());
            $order->setRewardpointsAmount($extensionAttributes->getRewardpointsAmount());
            $order->setRewardpointsBaseDiscountForShipping(
                $extensionAttributes->getRewardpointsBaseDiscountForShipping()
            );
            $order->setRewardpointsDiscountForShipping($extensionAttributes->getRewardpointsDiscountForShipping());
            $order->setCreditmemoRewardpointsEarn($extensionAttributes->getCreditmemoRewardpointsEarn());
            $order->setCreditmemoRewardpointsDiscount($extensionAttributes->getCreditmemoRewardpointsDiscount());
            $order->setCreditmemoRewardpointsBaseDiscount(
                $extensionAttributes->getCreditmemoRewardpointsBaseDiscount()
            );
        }

        $result[0] = $order;
        return $result;
    }

    /**
     * Before add item to order
     *
     * @param \Magestore\Webpos\Model\Checkout\PosOrderRepository $subject
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
     * @param array $items
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAddItemToOrder(
        \Magestore\Webpos\Model\Checkout\PosOrderRepository $subject,
        $order,
        $items
    ) {
        foreach ($items as &$item) {
            $extensionAttributes = $item->getExtensionAttributes();
            $item->setRewardpointsSpent($extensionAttributes->getRewardpointsSpent());
            $item->setRewardpointsEarn($extensionAttributes->getRewardpointsEarn());
            $item->setRewardpointsBaseDiscount($extensionAttributes->getRewardpointsBaseDiscount());
            $item->setRewardpointsDiscount($extensionAttributes->getRewardpointsDiscount());
        }
    }
}
