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
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_OrderSuccess::prepare_ship';
    
    /**
     * @var \Magestore\OrderSuccess\Service\ShipService
     */
    protected $shipService;

    /**
     * @param Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(
        Action\Context $context,
        ShippingChanelInterface $shippingChanelInterface,
        \Magestore\OrderSuccess\Service\ShipService $shipService
    ) {
        $this->shipService = $shipService;
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
        $responseAjax = new \Magento\Framework\DataObject();
        $data = $this->getRequest()->getParams();
        if(isset($data['packages'])){
            try {
                $this->shipService->processShipment($data);
                $responseAjax-> setMessage(__('The request has been created'));
                $responseAjax->setOk(true);
                $this->messageManager
                    ->addSuccessMessage(__('The request has been created'));
            }catch (\Exception $e){
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            }
        } else {
            $this->messageManager
                ->addWarningMessage(__('Can not create the ship request'));
        }
        $this->getResponse()->representJson($responseAjax->toJson());
    }
}
