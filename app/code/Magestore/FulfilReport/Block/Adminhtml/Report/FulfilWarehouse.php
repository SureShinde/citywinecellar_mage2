<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report;

/**
 * class \Magestore\Webpos\Block\Adminhtml\Report\SaleStaff
 *
 * @category    Magestore
 * @package     Magestore\Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class FulfilWarehouse extends \Magestore\FulfilReport\Block\Adminhtml\AbstractReport
{
    /**
     * contructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_fulfilwarehouse';
        $this->_blockGroup = 'Magestore_FulfilReport';
        $this->_headerText = __('Fulfilment by %1', $this->fulfilManagement->isMSIEnable() ? "Source" : "Warehouse");
        parent::_construct();
    }
}

