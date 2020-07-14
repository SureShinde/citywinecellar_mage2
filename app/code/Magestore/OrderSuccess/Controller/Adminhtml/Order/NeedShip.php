<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class NeedShip
 * @package Magestore\OrderSuccess\Controller\Adminhtml\Sales
 */
class NeedShip extends \Magestore\OrderSuccess\Controller\Adminhtml\Order\OrderAction
{   
    /**
     * Verify Sales
     *
     */
    public function execute()
    {
        $position = $this->_request->getParam('order_position')
            ? $this->_request->getParam('order_position')
            : 'needship';
        $orderId = $this->_request->getParam('order_id');
        if($orderId) {
            $this->shipService->removeFromBackOrder($orderId);
            $this->messageManager
                ->addSuccessMessage(__('The order has been backed to ship'));
        } else {
            $this->messageManager
                ->addWarningMessage(__('Cannot back to ship the order'));
        }
        return $this->_redirect('sales/order/view',
                                [
                                    'order_id' => $orderId,
                                    'order_position' => $position
                                ]);
                        }
}