<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Supplier;

/**
 * Class Logout
 * @package Magestore\DropshipSuccess\Controller\Supplier
 */
class Logout extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->supplierSession->setSupplier(null);
        $this->supplierSession->setSupplierId(null);
        $this->messageManager->addNoticeMessage(__('You have successfully logged out!'));
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('dropship/supplier/login');
        return $resultRedirect;
    }
}
