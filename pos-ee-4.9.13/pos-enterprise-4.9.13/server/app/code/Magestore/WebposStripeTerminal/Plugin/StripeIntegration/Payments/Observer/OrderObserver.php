<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Plugin\StripeIntegration\Payments\Observer;

/**
 * Class OrderObserver
 * @package Magestore\WebposStripeTerminal\Plugin\StripeIntegration\Payments\Observer
 */
class OrderObserver
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * OrderObserver constructor.
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    /**
     * @param \StripeIntegration\Payments\Observer\OrderObserver|Cryozonic\StripePayments\Observer\OrderObserver $subject
     * @param \Closure $proceed
     * @param $observer
     * @return mixed|null
     */
    public function aroundExecute(
        $subject,
        \Closure $proceed,
        $observer
    )
    {
        try {
            $order = $observer->getEvent()->getOrder();
            if (!$order->getPayment()) {
                return null;
            }
            return $proceed($observer);
        } catch (\Exception $e) {
            return $proceed($observer);
        }
    }
}