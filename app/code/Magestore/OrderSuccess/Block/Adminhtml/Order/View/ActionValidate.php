<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\View;

/**
 * Class Info
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales\View
 */
class ActionValidate extends Info
{
    
    /**
     * can cancel Order?
     * 
     * @return bool
     */
    public function canCancel()
    {
        return $this->getOrder()->canCancel();
    }
    
    /**
     * can hold Sales?
     * 
     * @return bool
     */
    public function canHold()
    {
        return $this->getOrder()->canHold();
    }
    
    /**
     * get Sales actions validation
     * 
     * @return string
     */
    public function getActionValidationJson()
    {
        $validation = [
            'canCancel' => $this->canCancel(),
            'canHold' => $this->canHold(),
        ];
        
        return \Zend_Json::encode($validation);
    }

}

