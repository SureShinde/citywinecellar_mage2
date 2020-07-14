<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\Button;
use Magestore\OrderSuccess\Api\PermissionManagementInterface;

class AllOrderButton extends ButtonAbstract
{

    /**
     * @return array
     */
    public function prepareButtonData()
    {
        if(!$this->isActive()) {
            return [];
        }
        
        $data = [
            'name' => __('all_order'),
            'label' => __('All Orders'),
            'class' => 'primary',
            'url' => 'ordersuccess/allorder',
            'sort_order' => 10,
        ];
        return $data;
    }  
       
    /**
     * 
     * @return bool
     */
    public function isActive()
    {
        $permissionManager = $this->objectManager->get('\Magestore\OrderSuccess\Api\PermissionManagementInterface');
        if($permissionManager->checkPermission(PermissionManagementInterface::ALL_ORDER_LIST)) {
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
        if(strtolower($this->request->getControllerName()) == 'allorder') {
            return true;
        }
        return false;
    }
    
}
