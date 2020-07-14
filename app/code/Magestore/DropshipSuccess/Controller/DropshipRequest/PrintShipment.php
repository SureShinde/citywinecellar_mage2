<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\DropshipRequest;

use Magento\Sales\Controller\OrderInterface;

/**
 * Class PrintShipment
 * @package Magestore\DropshipSuccess\Controller\DropshipRequest
 */
class PrintShipment extends \Magento\Sales\Controller\AbstractController\PrintShipment implements OrderInterface
{
    /**
     * Print Shipment Action
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        $shipmentId = (int)$this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            $order = $shipment->getOrder();
        } else {
            $orderId = (int)$this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        }

        $this->_coreRegistry->register('current_order', $order);
        if (isset($shipment)) {
            $this->_coreRegistry->register('current_shipment', $shipment);
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('print');
        return $resultPage;
    }
}
