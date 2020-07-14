<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\Package\Button;

use \Magestore\OrderSuccess\Block\Adminhtml\Order\Button\ButtonAbstract;

class PackageButton extends ButtonAbstract
{
    /**
     * @return array
     */
    public function prepareButtonData()
    {
        return [
            'name' => __('package'),
            'label' => __('Step %1. Delivery Packages', $this->getStep()),
            'class' => 'primary',
            'url' => 'fulfilsuccess/package',
            'sort_order' => 50,
        ];        
    }
    
    /**
     * is current this step
     * 
     * @return bool
     */
    public function isCurrent()
    {
        if(strtolower($this->request->getControllerName()) == 'package') {
            return true;
        }
        return false;
    }    
    
    /**
     * Get fulfil step
     * 
     * @return int
     */
    public function getStep()
    {
        if($this->scopeConfig->getValue(ButtonAbstract::VERITY_STEP_ENABLE_CONFIG_PATH)) {
            return 5;
        } else {
            return 4;
        }
    }       

}