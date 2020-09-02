<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Observer\Shipment;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface;

/**
 * Class SkipUpdateQtyToWarehouse
 * @package Magestore\DropshipSuccess\Observer\Shipment
 */
class SkipUpdateQtyToWarehouse implements ObserverInterface
{

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * SkipUpdateQtyToWarehouse constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer) 
    {
        if ($this->registry->registry(DropshipShipmentInterface::CREATE_SHIPMENT_BY_DROPSHIP)) {
            $skipWarehouse = $observer->getEvent()->getSkipWarehouse();
            $skipWarehouse = true;
            $observer->getEvent()->setData('skip_warehouse', $skipWarehouse);
        }
    }    
}