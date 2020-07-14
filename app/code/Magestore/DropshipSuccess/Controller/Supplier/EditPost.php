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
class EditPost extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

     /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $this->checkLogin();

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->supplierSession->isLoggedIn()) {
            $resultRedirect->setPath('dropship/supplier/index');
            return $resultRedirect;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPostValue();
            $supplier = $this->supplierSession->getSupplier();
            if ($data['new_password']) {
                $data['password'] = md5($data['new_password']);
            }
            $supplier->addData($data);
            try {
                $this->supplierRepository->save($supplier);
                $this->messageManager->addSuccessMessage('Supplier is edited!');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('Cannot edit supplier, please try again!');
            }
        } else {
            $this->messageManager->addErrorMessage('Cannot edit supplier, please try again!');
        }
        $resultRedirect->setPath('dropship/supplier/edit');
        return $resultRedirect;
    }
}
