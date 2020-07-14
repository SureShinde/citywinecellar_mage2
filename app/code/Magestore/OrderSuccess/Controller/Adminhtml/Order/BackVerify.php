<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class BackVerify
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class BackVerify extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{
    /**
     * Verify Sales
     *
     */
    public function execute()
    {
        $position = $this->_request->getParam('order_position')
            ? $this->_request->getParam('order_position')
            : 'needverify';
        $orderId = $this->_request->getParam('order_id');
        if($orderId) {
            $this->orderService->moveOrderToVerify($orderId);
            $this->messageManager
                ->addSuccessMessage(__('The order has been moved to verify'));
        } else {
            $this->messageManager
                ->addWarningMessage(__('Can not move the order to verify'));
        }
        return $this->_redirect('sales/order/view',
                                [
                                    'order_id' => $orderId,
                                    'order_position' => $position
                                ]);
    }
}