<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class RemoveFromBatch
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class RemoveFromBatch extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{

    /**
     * Add remove orders from Batch
     * 
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $orderIds = $collection->getAllIds();
        if(count($orderIds)) {
            $this->batchService->removeOrdersFromBatch($orderIds);
            $this->messageManager
                  ->addSuccessMessage(__('%1 Sales(s) has been removed from the Batch', count($orderIds)));
        } else {
            $this->messageManager
                 ->addWarningMessage(__('There is no Sales has been removed from the Batch'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}