<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;

use Magento\Customer\Model\Session;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

     /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->supplierSession->isLoggedIn()) {
            $resultRedirect->setPath('dropship/supplier/index');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    if ($this->supplierSession->login($login['username'], $login['password'])) {
                        $this->messageManager->addSuccessMessage(__('You have successfully logged in!'));
                        $resultRedirect->setPath('dropship/supplier/index');
                        return $resultRedirect;
                    }
                    $this->messageManager->addErrorMessage(__('Invalid username or password.'));
                    $resultRedirect->setPath('dropship/supplier/login');
                    return $resultRedirect;
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Invalid username or password.'));
                    $resultRedirect->setPath('dropship/supplier/login');
                    return $resultRedirect;
                }
            } else {
                $this->messageManager->addErrorMessage(__('Username and password are required.'));
            }
        }
        $resultRedirect->setPath('dropship/supplier/login');
        return $resultRedirect;
    }
}
