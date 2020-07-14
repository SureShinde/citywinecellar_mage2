<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

class Index extends AbstractSupplier
{

    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::supplier_listing';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_SupplierSuccess::supplier_listing');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Suppliers'));
        $resultPage->addBreadcrumb(__('Supplier Success'), __('Supplier Success'));
        $resultPage->addBreadcrumb(__('Manage Suppliers'), __('Manage Suppliers'));

        return $resultPage;
    }
}