<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest;

class DetailHolder extends \Magento\Backend\Block\Template
{
    
    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return __('Pack Request Information');
    }
}