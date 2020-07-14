<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

/**
 * Class View
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class View extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = parent::execute();
        $resultPage->getConfig()->getTitle()->prepend(__('Barcode Details'));
        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_BarcodeSuccess::manage_barcode');
    }
}
