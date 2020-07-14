<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface;

/**
 * Class CreditmemoWebposOrderPaymentSaveBefore
 * @package Magestore\WebposStripeTerminal\Observer
 */
class CreditmemoWebposOrderPaymentSaveBefore implements ObserverInterface
{
    /**
     * @var \Magestore\WebposStripeTerminal\Helper\Data
     */
    protected $helper;

    public function __construct()
    {
        $this->helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\WebposStripeTerminal\Helper\Data');
    }


    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magestore\Webpos\Model\Checkout\Order\Payment $paymentModel */
        $paymentModel = $observer->getData('webpos_payment');
        $isEnabled = $this->helper->isEnabled();
        if (!$isEnabled || $paymentModel->getMethod() !== StripeTerminalServiceInterface::CODE) {
            return $this;
        }

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->helper->getObjectManager()->create(OrderRepositoryInterface::class);
        /** @var Order $order */
        $order = $orderRepository->get($paymentModel->getOrderId());
        $comment = "[Refund] Ref: {$paymentModel->getReferenceNumber()} by {$paymentModel->getTitle()}";
        $order->addCommentToStatusHistory($comment, false, true);
        $orderRepository->save($order);
        return $this;
    }
}