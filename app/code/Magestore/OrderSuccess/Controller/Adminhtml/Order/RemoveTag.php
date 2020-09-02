<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class RemoveTag
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class RemoveTag extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
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
            $this->tagService->removeOrdersTag($orderIds);
            $this->messageManager
                  ->addSuccessMessage(__('%1 Sales(s) has been removed Tag', count($orderIds)));
        } else {
            $this->messageManager
                 ->addWarningMessage(__('There is no Sales has been removed Tag'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}