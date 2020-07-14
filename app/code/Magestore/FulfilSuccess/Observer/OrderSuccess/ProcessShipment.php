<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\OrderSuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\FulfilSuccess\Service\PickRequest\BuilderService;

class ProcessShipment implements ObserverInterface 
{
    
    /**
     * @var BuilderService 
     */
    protected $builderService;
    
    
    public function __construct(BuilderService $builderService)
    {
        $this->builderService = $builderService;
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
        $shipData = $observer->getEvent()->getShipData();
        
        $this->builderService->createPickRequestsFromPostData($shipData);

        return $this;
    }    
}