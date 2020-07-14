<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class BackVerify
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Order
 */
class MassBackVerify extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{
    /**
     * Verify Sales
     *
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $orderIds = $collection->getAllIds();
        if(count($orderIds)) {
            $this->orderService->moveOrdersToVerify($orderIds);
            $this->messageManager
                ->addSuccessMessage(__('%1 Sales(s) has been moved to verify step', count($orderIds)));
        } else {
            $this->messageManager
                ->addWarningMessage(__('No order has been moved to verify step'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}