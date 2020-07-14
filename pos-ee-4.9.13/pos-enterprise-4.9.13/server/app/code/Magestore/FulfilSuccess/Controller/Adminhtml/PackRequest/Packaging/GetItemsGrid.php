<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest\Packaging;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class GetItemsGrid extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::pack_request';

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magestore\FulfilSuccess\Model\Repository\PackRequest\PackRequestRepository
     */
    protected $packRequestRepository;

    /**
     * @param Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        Action\Context $context,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magestore\FulfilSuccess\Model\Repository\PackRequest\PackRequestRepository $packRequestRepository
    )
    {
        $this->shipmentLoader = $shipmentLoader;
        $this->packRequestRepository = $packRequestRepository;
        parent::__construct($context);
    }

    /**
     * Return grid with shipping items for Ajax request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $packRequestId = $this->_request->getParam('pack_request_id');
        if($packRequestId){
            $packRequest = $this->packRequestRepository->get($packRequestId);
            $orderId = $packRequest->getOrderId();
        }else{
            $orderId = $this->getRequest()->getParam('order_id');
        }
        $this->shipmentLoader->setOrderId($orderId);
        $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
        $this->shipmentLoader->load();
        return $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Packaging\Grid'
            )->setIndex(
                $this->getRequest()->getParam('index')
            )->toHtml()
        );
    }
}
