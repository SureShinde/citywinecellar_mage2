<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Verify;

class Detail extends \Magestore\OrderSuccess\Block\Adminhtml\Order\ViewModal
{
    
    /**
     * 
     * @return string@return string
     */
    public function getTitle()
    {
        return __('Verify Sales');
    }    
    
    /**
     * 
     * @return string
     */
    public function getModalId()
    {
        return 'verify_order_detail_holder';
    }   
    
    
    /**
     * Get json string of buttons
     * 
     * @return array
     */
    public function getButtons()
    {
        $buttons = [];
        $order = $this->getOrder();
        
        $buttons[] = [
            'name' => __('cancel'),
            'text' => __('Cancel Sales'),
            'class' => 'secondary cancel-order verify-action',
            'url' => $this->_urlBuilder->getUrl('ordersuccess/order_ajax/cancel', ['order_position' => 'needverify']),
            'confirm' => true,
        ];

        $buttons[] = [
            'name' => __('hold'),
            'text' => __('Hold Order'),
            'class' => 'secondary hold-order verify-action',
            'url' => $this->_urlBuilder->getUrl('ordersuccess/order_ajax/hold', ['order_position' => 'needverify']),
            'confirm' => true,
        ];             
        
        $buttons[] = [
            'name' => __('verify'),
            'text' => __('Mark as Verified'),
            'class' => 'primary verify-order verify-action',
            'url' => $this->_urlBuilder->getUrl('ordersuccess/order_ajax/verify', ['order_position' => 'needverify']),
            'confirm' => true,
        ];          
        
        return \Zend_Json::encode($buttons);
    }
}