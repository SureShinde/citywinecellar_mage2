<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\Button;
use Magestore\OrderSuccess\Api\PermissionManagementInterface;

class PrepareShipButton extends ButtonAbstract
{

    /**
     * @return array
     */
    public function prepareButtonData()
    {
        return [
            'name' => __('prepare_ship'),
            'label' => __('Prepare Ship'),
            'class' => 'primary',
            'url' => 'ordersuccess/needship',
            'sort_order' => 20,
        ];        
    }
    
    /**
     * 
     * @return bool
     */
    public function isActive()
    {
        $permissionManager = $this->objectManager->get('\Magestore\OrderSuccess\Api\PermissionManagementInterface');
        if($permissionManager->checkPermission(PermissionManagementInterface::PREPARE_SHIP_LIST)) {
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
        if(strtolower($this->request->getControllerName()) == 'needship') {
            return true;
        }
        return false;
    }    
    
}
