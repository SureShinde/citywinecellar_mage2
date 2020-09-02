<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

/**
 * Class MassVerify
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class MassVerify extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_OrderSuccess::verify_order';

    /**
     * Verify Order
     *
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $orderIds = $collection->getAllIds();
        if (count($orderIds)) {
            $this->orderService->verifyOrders($orderIds);
            $this->messageManager->addSuccessMessage(__('%1 Sales(s) has been verified', count($orderIds)));
        } else {
            $this->messageManager->addWarningMessage(__('There is no Sales has been verified'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}