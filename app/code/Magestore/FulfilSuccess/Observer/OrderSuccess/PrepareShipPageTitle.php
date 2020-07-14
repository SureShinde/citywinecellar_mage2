<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\OrderSuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class PrepareShipPageTitle implements ObserverInterface 
{

    /**
     * 
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer) 
    {
        $pageTitle = $observer->getEvent()->getPageTitle();

        $pageTitle->setData('title', __('Fulfillment - Prepare Fulfil'));
        
        return $this;
    }    
}