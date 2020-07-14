<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Controller\Adminhtml\Supplier;

use Magento\Framework\Controller\ResultFactory;
use \Magestore\SupplierSuccess\Controller\Adminhtml\AbstractSupplier;

/**
 * Class Import
 * @package Magestore\SupplierSuccess\Controller\Adminhtml\Supplier
 */
class Import extends AbstractSupplier
{
    const ADMIN_RESOURCE = 'Magestore_SupplierSuccess::view_supplier';
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            try {
                $file = $this->getRequest()->getFiles('import_product');
                $supplierId = $this->getRequest()->getParam('id');
                if ($file) {
                    $result = $this->_supplierProductService->importProductToSupplier($supplierId, $file);
                    if($result>0)
                        $this->messageManager->addSuccessMessage(__('%1 product(s) have been imported.', $result));
                    else
                        $this->messageManager->addWarningMessage(__('No product has been imported'));
                } else {
                    $this->messageManager->addErrorMessage(__('Please choose import file'));
                }

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
