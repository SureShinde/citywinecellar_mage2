<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\DropshipRequest;

/**
 * Class ViewShipment
 * @package Magestore\DropshipSuccess\Controller\DropshipRequest
 */
class ViewShipment extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
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
        $dropshipRequest = $this->dropshipRequestRepository->getById($dropshipRequestId);
        $this->coreRegistry->register('current_dropship_request', $dropshipRequest);

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('View shipment'));
        return $resultPage;
    }
}
