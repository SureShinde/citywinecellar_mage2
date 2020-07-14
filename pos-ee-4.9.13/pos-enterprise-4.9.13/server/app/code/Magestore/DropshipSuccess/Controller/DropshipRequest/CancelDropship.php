<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\DropshipRequest;

/**
 * Class CancelDropship
 * @package Magestore\DropshipSuccess\Controller\DropshipRequest
 */
class CancelDropship extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$dropshipRequestId) {
            $this->messageManager->addErrorMessage(__('Cannot cancel the dropship request.'));
            return $this->_redirect('*/*/*');
        }
        try {
            $this->dropshipRequestService->backToPrepareFulfil($dropshipRequestId);

            /** send email to admin to inform cancel dropship */
            $this->emailService->sendCancelDropshipToAdmin($this->dropshipRequestRepository->getById($dropshipRequestId));

            $this->messageManager->addSuccessMessage(__('The dropship request has been canceled'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->messageManager->addErrorMessage(__('Cannot cancel the dropship request.'));
        }
        return $this->_redirect('*/*/viewDropship', ['dropship_id' => $dropshipRequestId]);
    }
}
