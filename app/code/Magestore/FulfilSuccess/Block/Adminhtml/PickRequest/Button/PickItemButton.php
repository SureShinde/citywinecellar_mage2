<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\Button;

use Magestore\OrderSuccess\Block\Adminhtml\Order\Button\ButtonAbstract;
use Magestore\FulfilSuccess\Api\PermissionManagementInterface;

class PickItemButton extends ButtonAbstract
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
            'name' => __('pick_item'),
            'label' => __('Step %1. Pick Items', $this->getStep()),
            'class' => 'primary',
            'url' => 'fulfilsuccess/pickRequest',
            'sort_order' => 30,
            'visible' => false,
            'disable' => true,
        ];        
    }
    
    
    /**
     * 
     * @return bool
     */
    public function isActive()
    {
        $permissionManager = $this->objectManager->get('\Magestore\FulfilSuccess\Api\PermissionManagementInterface');
        if($permissionManager->checkPermission(PermissionManagementInterface::PICK_ITEM)) {
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
        if(strtolower($this->request->getControllerName()) == 'pickrequest') {
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
            return 3;
        } else {
            return 2;
        }
    }    

}