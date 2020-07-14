<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposTyro\Plugin\Sales\Model;

class OrderPlugin {
    protected $objectManager;

    /**
     * OrderPlugin constructor.
     */
    public function __construct()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    /**
     * For some payments, they don't agree cancelling order action if pay partially
     * @param \Magento\Sales\Model\Order $subject
     * @param $result
     * @return boolean
     */
    public function afterCanCancel($subject, $result)
    {

        if (!$result) {
            return $result;
        }

        if (
            !$subject->getPayment()
            ||
            $subject->getPayment()->getMethod() !== \Magestore\Payment\Model\Payment\Method\MultiPayment::PAYMENT_CODE
        ) {
            return $result;
        }
        /** @var \Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Collection $orderPayments */
        $orderPayments = $this->objectManager
            ->get('\Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\CollectionFactory')
            ->create()
            ->addFieldToFilter('order_id', $subject->getId())
            ->addFieldToFilter('method', \Magestore\WebposTyro\Helper\Data::PAYMENT_CODE);


        if (!$orderPayments->count()) {
            return $result;
        }

        $paid = 0;
        $refunded = 0;
        /** @var \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface $orderPayment */
        foreach ($orderPayments as $orderPayment) {
            if (
                (int)$orderPayment->getType()
                === \Magestore\Webpos\Api\Data\Checkout\Order\PaymentInterface::TYPE_CHECKOUT
            ) {
                $paid += $orderPayment->getAmountPaid();
                continue;
            }

            $refunded += $orderPayment->getAmountPaid();
        }

        return $paid <= $refunded;
    }
}