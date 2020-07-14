<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\History;

/**
 * Class View
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class View extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractHistory
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = parent::execute();
        $resultPage->getConfig()->getTitle()->prepend(__('Barcode Created History Details'));
        $id = $this->getRequest()->getParam('id');
        if($id){
            $this->locator->setCurrentBarcodeHistory($id);
        }
        return $resultPage;
    }
}
