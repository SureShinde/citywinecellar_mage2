<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist;

use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

class Index extends AbstractSupplier
{

    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::supplier_pricinglist';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_SupplierSuccess::supplier_pricinglist');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Pricelist'));
        $resultPage->addBreadcrumb(__('Supplier Success'), __('Supplier Success'));
        $resultPage->addBreadcrumb(__('Manage Pricelist'), __('Manage Pricelist'));

        return $resultPage;
    }
}