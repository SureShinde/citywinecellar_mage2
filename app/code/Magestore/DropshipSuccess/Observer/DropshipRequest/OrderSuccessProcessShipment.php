<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Observer\DropshipRequest;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\DropshipSuccess\Service\DropshipRequestService;

/**
 * Class OrderSuccessProcessShipment
 * @package Magestore\DropshipSuccess\Observer\DropshipRequest
 */
class OrderSuccessProcessShipment implements ObserverInterface
{

    /**
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;

    /**
     * OrderSuccessProcessShipment constructor.
     * @param DropshipRequestService $dropshipRequestService
     */
    public function __construct(
        DropshipRequestService $dropshipRequestService
    ) {
        $this->dropshipRequestService = $dropshipRequestService;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $shipData = $observer->getEvent()->getData('ship_data');

        $this->dropshipRequestService->createDropshipRequestsFromPostData($shipData);

        return $this;

    }

}