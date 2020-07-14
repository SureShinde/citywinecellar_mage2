<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Button;

use \Magestore\OrderSuccess\Block\Adminhtml\Order\Button\ButtonAbstract;
use Magestore\FulfilSuccess\Api\PermissionManagementInterface;

class PackItemButton extends ButtonAbstract
{
    /**
     * @return array
     */
    public function prepareButtonData()
    {
        if(!$this->isActive()) {
            return [];
        }
        
        return [
            'name' => __('pack_item'),
            'label' => __('Step %1. Pack Items', $this->getStep()),
            'class' => 'primary',
            'url' => 'fulfilsuccess/packRequest',
            'sort_order' => 40,
        ];        
    }
    
    /**
     * 
     * @return bool
     */
    public function isActive()
    {
        $permissionManager = $this->objectManager->get('\Magestore\FulfilSuccess\Api\PermissionManagementInterface');
        if($permissionManager->checkPermission(PermissionManagementInterface::PACK_ITEM)) {
            return true;
        }
        return false;
    }    
    
    /**
     * is current this step
     * 
     * @return bool
     */
    public function isCurrent()
    {
        if(strtolower($this->request->getControllerName()) == 'packrequest') {
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
            return 4;
        } else {
            return 3;
        }
    }    
    

}