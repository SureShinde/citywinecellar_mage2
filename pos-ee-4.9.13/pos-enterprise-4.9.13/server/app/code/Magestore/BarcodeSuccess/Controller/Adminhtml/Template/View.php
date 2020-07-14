<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Template;

/**
 * Class View
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Template
 */
class View extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = parent::execute();
        $id = $this->getRequest()->getParam('id');
        if($id){
            $this->locator->add('current_barcode_template',$id);
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Barcode Template'));
        }else{
            $this->locator->remove('current_barcode_template');
            $resultPage->getConfig()->getTitle()->prepend(__('Add a New Barcode Template'));
        }
        return $resultPage;
    }
}
