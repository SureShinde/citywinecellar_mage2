<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order\Ajax;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class UpdateTag
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales\Ajax
 */
class Hold extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{
    
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $orderId = $this->_request->getParam('order_id');
        $return = [];
        try {
            $this->orderManagement->hold($orderId);
            $return = ['message'=> __('You put the order on hold.')];
        } catch (\Exception $e) {
            $return = [
                'message'=> $e->getMessage(),
                //'message' => __('You have not put the order on hold.'),
                'error' => true,
            ];
        }   
        return $resultJson->setData($return);
    }
}