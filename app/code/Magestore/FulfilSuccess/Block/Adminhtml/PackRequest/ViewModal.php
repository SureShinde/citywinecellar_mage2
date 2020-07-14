<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest;

class ViewModal extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\DetailHolder
{
    
    /**
     * 
     * @return string@return string
     */
    public function getTitle()
    {
        return __('Pack Request Information');
    }    
    
    /**
     * 
     * @return string
     */
    public function getModalId()
    {
        return 'pack_request_detail_holder';
    }
    
}
