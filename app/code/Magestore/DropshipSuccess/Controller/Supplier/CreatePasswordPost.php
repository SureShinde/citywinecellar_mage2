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
class CreatePasswordPost extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParam('forgot', null);
        $password = (string)$this->getRequest()->getPost('password', null);
        $passwordConfirmation = (string)$this->getRequest()->getPost('password_confirmation', null);
        if (!$data || !$password) {
            $this->messageManager->addErrorMessage(__('You cannot reset password!'));
            $resultRedirect->setPath('dropship/supplier/createPassword', ["_current" => true]);
            return $resultRedirect;
        }

        if ($password !== $passwordConfirmation) {
            $this->messageManager->addErrorMessage(__("New Password and Confirm New Password didn't match."));
            $resultRedirect->setPath('dropship/supplier/createPassword', ["_current" => true]);
            return $resultRedirect;
        }
        if (iconv_strlen($password) <= 0) {
            $this->messageManager->addErrorMessage(__('Please enter a new password.'));
            $resultRedirect->setPath('dropship/supplier/createPassword', ["_current" => true]);
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
            $supplier->setPassword(md5($password));
            $this->supplierRepository->save($supplier);
            $this->messageManager->addSuccessMessage(__('You updated your password.'));
            $resultRedirect->setPath('dropship/supplier/login');
            return $resultRedirect;
        } else {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the new password.'));
            $resultRedirect->setPath('dropship/supplier/createPassword', ["_current" => true]);
            return $resultRedirect;
        }
    }
}
