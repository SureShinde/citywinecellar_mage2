<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;

use Magento\Customer\Model\Session;
use Magestore\SupplierSuccess\Api\Data\SupplierInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatePassword extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    /**
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParam('forgot', null);
        if (!$data) {
            $resultRedirect->setPath('dropship/supplier/index');
            return $resultRedirect;
        }
        $data = $this->dropshipRequestService->decodeForgotPasswordUrl($data);
        $supplierId = $data['supplier_id'];
        $supplierCode = $data['supplier_code'];
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierCollection */
        $supplierCollection = $this->supplierCollectionFactory->create();
        /** @var SupplierInterface $supplier */
        $supplier = $supplierCollection->addFieldToFilter(SupplierInterface::SUPPLIER_ID, $supplierId)
            ->addFieldToFilter(SupplierInterface::SUPPLIER_CODE, $supplierCode)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
        if ($supplier->getId()) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Reset Password'));
            return $resultPage;
        } else {
            $this->messageManager->addErrorMessage(__('Your password reset link has expired.'));
        }
        $resultRedirect->setPath('dropship/supplier/login');
        return $resultRedirect;
    }
}
