<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\OrderSuccess;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\OrderSuccess\Block\Adminhtml\Order\Button\ButtonAbstract;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PrepareButton implements ObserverInterface 
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }
    
    /**
     * 
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer) 
    {
        $buttonData = $observer->getEvent()->getButtonData();

        if($buttonData->getName() == 'verify_order') {
            $buttonData->setData('label', __('Step 1. Verify Orders'));
        }
        if($buttonData->getName() == 'prepare_ship') {
            $step = $this->scopeConfig->getValue(ButtonAbstract::VERITY_STEP_ENABLE_CONFIG_PATH) ? 2 : 1;
            $buttonData->setData('label', __('Step %1. Prepare Fulfil', $step));
        }        
        return $this;
    }    
}