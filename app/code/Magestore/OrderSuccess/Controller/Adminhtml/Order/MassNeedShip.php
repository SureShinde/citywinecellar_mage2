<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class MassNeedShip
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class MassNeedShip extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{
    /**
     * Verify Order
     *
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $orderIds = $collection->getAllIds();
        if(count($orderIds)) {
            $this->shipService->removeFromBackOrders($orderIds);
            $this->messageManager
                ->addSuccessMessage(__('%1 Sales(s) has been backed to ship', count($orderIds)));
        } else {
            $this->messageManager
                ->addWarningMessage(__('There is no Sales has been backed to ship'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}