<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Block\Adminhtml\Barcode;
class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    public function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Magestore_BarcodeSuccess';
        $this->_controller = 'adminhtml_barcode';
        $this->_mode = 'import';
        $this->buttonList->update('save', 'label', __('Import'));
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');

    }
}