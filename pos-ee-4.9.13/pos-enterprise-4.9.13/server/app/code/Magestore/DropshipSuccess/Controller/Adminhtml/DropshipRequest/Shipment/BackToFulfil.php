<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest\Shipment;

/**
 * Class BackToFulfil
 * @package Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest\Shipment
 */
class BackToFulfil extends \Magestore\DropshipSuccess\Controller\Adminhtml\AbstractDropship
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_DropshipSuccess::dropship_request_listing';

    /**
     * View order detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $requestId = $this->getRequest()->getParam('dropship_request_id');
        if (!$requestId) {
            $this->messageManager->addErrorMessage(__('Cannot cancel the dropship request.'));
            return $resultRedirect->setPath('dropshipsuccess/dropshiprequest/edit', ['_current' => true]);
        }
        try {
            $this->_objectManager->get('Magestore\DropshipSuccess\Service\DropshipRequestService')
                ->backToPrepareFulfil($requestId);
            $this->messageManager->addSuccessMessage(__('The dropship request has been canceled'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->messageManager->addErrorMessage(__('Cannot cancel the dropship request.'));
        }
        return $resultRedirect->setPath('dropshipsuccess/dropshiprequest/edit', ['_current' => true]);
    }
}
