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
class MassCancelBatch extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{

    /**
     * Add remove orders from Batch
     * 
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
//        \Zend_Debug::dump($collection->getSize());die();
        if($collection->getSize()) {
            $batchIds = $this->orderService->getAllBatchIds($collection);
            $this->batchService->cancelBatchs($batchIds);
            $this->messageManager
                  ->addSuccessMessage(__('%1 batch(s) has been canceled', count($batchIds)));
        } else {
            $this->messageManager
                 ->addWarningMessage(__('No batch has been canceled'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}