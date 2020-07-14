<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order;

class ViewModal extends \Magento\Backend\Block\Template
{
    
     /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    
    
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $coreRegistry,
            array $data = []
    )
    {    
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;            
    }
    
    /**
     * 
     * @return string@return string
     */
    public function getTitle()
    {
        return __('Order Information');
    }    
    
    /**
     * 
     * @return string
     */
    public function getModalId()
    {
        return 'view_order_detail_holder';
    }    
}