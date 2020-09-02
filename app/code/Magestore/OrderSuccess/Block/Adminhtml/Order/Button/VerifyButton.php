<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\Button;
use Magestore\OrderSuccess\Api\PermissionManagementInterface;

class VerifyButton extends ButtonAbstract
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
            'name' => __('verify_order'),
            'label' => __('Verify Orders'),
            'class' => 'primary',
            'url' => 'ordersuccess/needverify',
            'sort_order' => 10,
        ];    
        if(!$this->scopeConfig->getValue(self::VERITY_STEP_ENABLE_CONFIG_PATH)) {
            $data['style'] = 'display:none;';
        }
        return $data;
    }  
    
    /**
     * 
     * @return bool
     */
    public function isActive()
    {
        $permissionManager = $this->objectManager->get('\Magestore\OrderSuccess\Api\PermissionManagementInterface');
        if($permissionManager->checkPermission(PermissionManagementInterface::VERIFY_ORDER_LIST)) {
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
        if(strtolower($this->request->getControllerName()) == 'needverify') {
            return true;
        }
        return false;
    }
    
}
