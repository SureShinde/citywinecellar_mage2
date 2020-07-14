<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\OrderSuccess\Service\OrderService;

class SalesPlaceOrderAfter implements ObserverInterface
{

    /**
     * @var OrderService
     */
    protected $orderService;


    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Process pick request from Sales Success
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if($order->getId() && $order->getData('pos_id')) {
            $this->orderService->verifyOrder($order->getId());
        }
        return $this;
    }
}