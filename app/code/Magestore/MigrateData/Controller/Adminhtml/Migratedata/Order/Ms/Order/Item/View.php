<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\MigrateData\Controller\Adminhtml\Migratedata\Order\Ms\Order\Item;

/**
 * Class View
 *
 * @package Magestore\MigrateData\Controller\Adminhtml\Migratedata\Supplier
 */
class View extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
   
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_MigrateData::migrate_data');
        return $resultPage;
    }
    
    /**
     * View purchase order form
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        return $resultPage;
    }
}