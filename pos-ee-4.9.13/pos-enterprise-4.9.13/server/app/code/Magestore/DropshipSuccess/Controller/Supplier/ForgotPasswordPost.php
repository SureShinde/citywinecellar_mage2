<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;

use Magestore\DropshipSuccess\Controller\AbstractSupplier;
use Magestore\SupplierSuccess\Api\Data\SupplierInterface;

/**
 * ForgotPasswordPost controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ForgotPasswordPost extends AbstractSupplier
{
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var Escaper */
    protected $escaper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Forgot customer password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $email = (string)$this->getRequest()->getPost('email');
        if ($email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $this->messageManager->addErrorMessage(__('Please correct the email address.'));
                return $resultRedirect->setPath('*/*/forgotPassword');
            }

            try {
                /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierCollection */
                $supplierCollection = $this->supplierCollectionFactory->create();
                /** @var SupplierInterface $supplier */
                $supplier = $supplierCollection->addFieldToFilter(SupplierInterface::CONTACT_EMAIL, $email)
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem();
                if ($supplier->getId()) {
                    $forgotPasswordUrl = $this->dropshipRequestService->getForgotPasswordUrl($supplier);
                    $this->emailService->sendEmailForgotEmailToSupplier($supplier, $forgotPasswordUrl);
                } else {
                    $this->messageManager->addErrorMessage(__('Invalid email'));
                    return $resultRedirect->setPath('*/*/forgotPassword');
                }
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We\'re unable to send the password reset email.')
                );
                return $resultRedirect->setPath('*/*/forgotPassword');
            }
            $this->messageManager->addSuccessMessage($this->getSuccessMessage($email));
            return $resultRedirect->setPath('*/*/');
        } else {
            $this->messageManager->addErrorMessage(__('Please enter your email.'));
            return $resultRedirect->setPath('*/*/forgotPassword');
        }
    }

    /**
     * Retrieve success message
     *
     * @param string $email
     * @return \Magento\Framework\Phrase
     */
    public function getSuccessMessage($email)
    {
        return __(
            'If there is an account associated with %1 you will receive an email with a link to reset your password.',
            $email
        );
    }
}
