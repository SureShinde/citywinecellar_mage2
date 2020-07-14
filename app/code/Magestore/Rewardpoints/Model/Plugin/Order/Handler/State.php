<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Rewardpoints\Model\Plugin\Order\Handler;

use Magento\Sales\Model\Order;

/**
 * Class State
 * @package Magestore\Rewardpoints\Model\Plugin\Order\Handler
 */
class State
{
    /**
     * @param \Magento\Checkout\Model\PaymentInformationManagement $paymentInformationManagement
     * @param $paymentDetails
     * @param $cartId
     * @return $paymentDetails
     */
    public function aroundCheck(
        \Magento\Sales\Model\ResourceModel\Order\Handler\State $stateHandler,
        \Closure $proceed,
        Order $order
    )
    {
        $handler = $proceed($order);
        if ($order->getRewardpointsSpent() < 0.0001 || $order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED || $order->isCanceled() || $order->canUnhold()
        ) {
            return $this;
        }
        if (!$order->isCanceled() && !$order->canUnhold() && !$order->canInvoice() && !$order->canShip()) {
            if (0 == $order->getBaseGrandTotal() && $order->getState() == Order::STATE_COMPLETE) {
                $isClosed = true;
                foreach ($order->getAllItems() as $item) {
                    if ($item->getParentItemId())
                        continue;
                    if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                        foreach ($item->getChildren() as $child) {
                            if ($child->getQtyInvoiced() - $child->getQtyRefunded() > 0) {
                                $isClosed = false;
                            }
                        }
                    } elseif ($item->getRewardpointsSpent()) {
                        if (($item->getQtyInvoiced() - $item->getQtyRefunded()) > 0) {
                            $isClosed = false;
                        }
                    }
                }
                if ($isClosed) {
                    $order->setState(Order::STATE_CLOSED)
                        ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED));
                }
            }
        }
        return $handler;
    }
}