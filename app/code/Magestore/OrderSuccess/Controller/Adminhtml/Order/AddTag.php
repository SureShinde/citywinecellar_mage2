<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class AddTag
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class AddTag extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{
    /**
     * Add orders to the Batch
     * 
     */
    public function execute()
    {
        $tag = $this->_request->getParam('tag');
        if($tag) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $orderIds = $collection->getAllIds();
            $this->tagService->addTagByOrderIds($tag, $orderIds);
            if (count($orderIds)) {
                $this->messageManager
                    ->addSuccessMessage(__('%1 Sales(s) has been added to the Tag', count($orderIds)));
            } else {
                $this->messageManager
                    ->addWarningMessage(__('There is no Sales has been added to the Tag'));
            }
        }else {
            $this->messageManager
                ->addWarningMessage(__('No tag has been selected'));
        }
        $this->_redirect($this->getComponentRefererUrl());
    }
}