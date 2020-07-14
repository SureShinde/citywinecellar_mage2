<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

/**
 * Class Import
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class Import extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
    
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_BarcodeSuccess::import_barcode';
    
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = parent::execute();
        $resultPage->getConfig()->getTitle()->prepend(__('Import Barcode'));
        return $resultPage;
    }
}
