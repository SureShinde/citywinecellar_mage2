<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist;

use Magento\Framework\Controller\ResultFactory;
use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

/**
 * Class Import
 * @package Magestore\SupplierSuccess\Controller\Adminhtml\Supplier\Pricinglist
 */
class Import extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::supplier_pricinglist_edit';

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $file = $this->getRequest()->getFiles('import_product');
                if ($file) {
                    $this->supplierPricingListService->importPricingListToSupplier($file);
                }
                $this->messageManager->addSuccessMessage(__('The pricelist has been imported.'));

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid file upload attempt'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;

    }
}
