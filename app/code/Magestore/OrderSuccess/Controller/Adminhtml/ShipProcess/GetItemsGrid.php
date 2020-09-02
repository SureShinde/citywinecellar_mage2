<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Controller\Adminhtml\ShipProcess;

use Magento\Backend\App\Action;
use Magestore\OrderSuccess\Api\Data\ShippingChanelInterface;

/**
 * Class GetItemsGrid
 * @package Magestore\OrderSuccess\Controller\Adminhtml\ShipProcess
 */
class GetItemsGrid extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_OrderSuccess::prepare_ship';

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @param Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        Action\Context $context,
        ShippingChanelInterface $shippingChanelInterface,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
    ) {
        $this->shipmentLoader = $shipmentLoader;
        $this->shippingChanelInterface = $shippingChanelInterface;
        parent::__construct($context);
    }

    /**
     * Return grid with shipping items for Ajax request
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $containerId = $this->getRequest()->getParam('container');
        $shippingChanels = $this->shippingChanelInterface->getOptionBlockArray();
        $gridBlock = 'Magestore\OrderSuccess\Block\Adminhtml\Order\Grid';
        if(isset($shippingChanels[$containerId])) {
            $gridBlock = $shippingChanels[$containerId];
        }
        return $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                $gridBlock
            )->setIndex(
                $this->getRequest()->getParam('index')
            )->toHtml()
        );
    }
}
