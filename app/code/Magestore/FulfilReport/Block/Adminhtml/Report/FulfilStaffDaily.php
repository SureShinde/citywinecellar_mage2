<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

class FulfilStaffDaily extends \Magestore\FulfilReport\Block\Adminhtml\AbstractReport
{
    /**
     * contructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_fulfilstaffdaily';
        $this->_blockGroup = 'Magestore_FulfilReport';
        $this->_headerText = __('Fulfilment by staff (Daily)');
        parent::_construct();
    }
}

