<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\DropshipRequest;

/**
 * Class ViewDropship
 * @package Magestore\DropshipSuccess\Controller\DropshipRequest
 */
class ViewDropship extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->checkLogin();

        $dropshipRequestId = $this->getRequest()->getParam('dropship_id');

        try {
            $dropshipRequest = $this->dropshipRequestRepository->getById($dropshipRequestId);
            if (!$this->dropshipRequestRepository->isAllowedAccess($dropshipRequest, $this->supplierSession->getSupplier()->getId())) {
                $this->_objectManager->get('Magento\Framework\Message\ManagerInterface')->addErrorMessage(__('You don\'t have the permission!'));
                $this->_objectManager->create('Magento\Framework\Controller\Result\Forward')->forward('noroute');
            }
            $this->coreRegistry->register('current_dropship_request', $dropshipRequest);
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Message\ManagerInterface')->addErrorMessage(__($e->getMessage()));
            $this->_objectManager->create('Magento\Framework\Controller\Result\Forward')->forward('noroute');
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('View dropship details'));
        return $resultPage;
    }
}
