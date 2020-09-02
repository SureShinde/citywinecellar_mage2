<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class AddToBatch
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class AddToBatch extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{
    /**
     * Add orders to the Batch
     * 
     */
    public function execute()
    {
        $batchId = $this->_request->getParam('batch_id');
        if(!$batchId) {
            $batch = $this->batchRepository->newBatch();
        } else {
            $batch = $this->batchRepository->getById($batchId);
        }
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $orderIds = $collection->getAllIds();
        $this->batchService->addOrdersToBatch($batch, $orderIds);
        if(count($orderIds)) {
            $this->messageManager
                ->addSuccessMessage(__('%1 Order(s) has been moved to the batch', count($orderIds)));
        } else {
            $this->messageManager
                ->addWarningMessage(__('There is no Sales has been moved to the Batch'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}