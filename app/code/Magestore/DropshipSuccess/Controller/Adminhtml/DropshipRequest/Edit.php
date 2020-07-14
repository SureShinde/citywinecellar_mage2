<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest;

/**
 * Class Edit
 * @package Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest
 */
class Edit extends \Magestore\DropshipSuccess\Controller\Adminhtml\AbstractDropship
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_DropshipSuccess::view_dropship_request';

    /**
     * View order detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->_initDropshipRequest();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($dropshipRequest) {
            try {
                $resultPage = $this->_initAction();
                $resultPage->getConfig()->getTitle()->prepend(__('Dropship Request'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Exception occurred when loading order'));
                $resultRedirect->setPath('dropshipsuccess/dropshiprequest/index');
                return $resultRedirect;
            }
            $resultPage->getConfig()->getTitle()->prepend(__("Dropship Request #%1 for Sales #%2", $dropshipRequest->getId(), $dropshipRequest->getOrderIncrementId()));
            return $resultPage;
        }
        $resultRedirect->setPath('dropshipsuccess/dropshiprequest/index');
        return $resultRedirect;
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_DropshipSuccess::Dropship');
        $resultPage->addBreadcrumb(__('Dropship Success'), __('Dropship Success'));
        $resultPage->addBreadcrumb(__('Dropship Request'), __('Dropship Request'));
        return $resultPage;
    }
}
