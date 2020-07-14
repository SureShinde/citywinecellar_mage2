<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class Verify
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class Verify extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{
    
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_OrderSuccess::verify_order';
    
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
            $this->orderService->verifyOrder($orderId);
            $this->messageManager
                ->addSuccessMessage(__('The order has been verified'));
        } else {
            $this->messageManager
                ->addWarningMessage(__('Can not verify the order'));
        }
        return $this->_redirect('sales/order/view',
                                [
                                    'order_id' => $orderId,
                                    'order_position' => $position
                                ]);
                        }
}