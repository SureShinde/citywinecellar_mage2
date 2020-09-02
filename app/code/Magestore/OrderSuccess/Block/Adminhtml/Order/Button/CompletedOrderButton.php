<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\Button;
use Magestore\OrderSuccess\Api\PermissionManagementInterface;

class CompletedOrderButton extends ButtonAbstract
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
            'name' => __('completed_order'),
            'label' => __('Completed Orders'),
            'class' => 'primary',
            'url' => 'ordersuccess/completed',
            'sort_order' => 40,
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
        if($permissionManager->checkPermission(PermissionManagementInterface::COMPLETED_ORDER_LIST)) {
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
        if(strtolower($this->request->getControllerName()) == 'completed') {
            return true;
        }
        return false;
    }
    
}
