<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest;

class ViewModal extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\DetailHolder
{
    
    /**
     * 
     * @return string@return string
     */
    public function getTitle()
    {
        return __('Pick Request Information');
    }    
    
    /**
     * 
     * @return string
     */
    public function getModalId()
    {
        return 'pick_request_detail_holder';
    }
    
}
