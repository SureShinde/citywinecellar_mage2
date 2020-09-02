<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest\Shipment;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;
use Magestore\DropshipSuccess\Service\DropshipRequestService;

/**
 * Class Save
 * @package Magestore\DropshipSuccess\Controller\Adminhtml\DropshipRequest\Shipment
 */
class Save extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_DropshipSuccess::save_dropship_shipment';

    /**
     * @var \Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipmentFactory
     */
    protected $dropshipShipmentFactory;

    /**
     * @var \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipShipmentService
     */
    protected $dropshipShipmentService;

    /**
     * @var \Magestore\DropshipSuccess\Api\DropshipShipmentRepositoryInterface
     */
    protected $dropshipShipmentRepository;

    /**
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepository;

    /**
     * @param Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param ShipmentSender $shipmentSender
     */
    public function __construct(
        Action\Context $context,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        ShipmentSender $shipmentSender,
        \Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipmentFactory $dropshipShipmentFactory,
        \Magestore\DropshipSuccess\Service\DropshipRequest\DropshipShipmentService $dropshipShipmentService,
        \Magestore\DropshipSuccess\Api\DropshipShipmentRepositoryInterface $dropshipShipmentRepository,
        DropshipRequestService $dropshipRequestService,
        \Magento\Framework\Registry $coreRegistry,
        DropshipRequestRepositoryInterface $dropshipRequestRepository
    ) {
        parent::__construct($context, $shipmentLoader, $labelGenerator, $shipmentSender);
        $this->dropshipShipmentFactory = $dropshipShipmentFactory;
        $this->dropshipShipmentService = $dropshipShipmentService;
        $this->dropshipShipmentRepository = $dropshipShipmentRepository;
        $this->dropshipRequestService = $dropshipRequestService;
        $this->coreRegistry = $coreRegistry;
        $this->dropshipRequestRepository = $dropshipRequestRepository;

    }
    
    /**
     * View order detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $isValidFormKey = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$isValidFormKey || !$isPost) {
            $this->messageManager->addErrorMessage(__('We can\'t save the dropship shipment right now.'));
            return $resultRedirect->setPath('dropshipsuccess/dropshiprequest/edit', ['_current' => true]);
        }

        $data = $this->getRequest()->getParams();
        $data = $this->dropshipShipmentService->validateShipmentData($data);
        try {
            /** skip update qty to warehouse after create shipment by dropship */
            $this->coreRegistry->register(DropshipShipmentInterface::CREATE_SHIPMENT_BY_DROPSHIP, true);

            $shipment = $this->createShipment($data);
            
            $dropship = $this->dropshipShipmentFactory->create();
            $dropship->setShipmentId($shipment->getId());
            $dropship->addData($data);
            $this->dropshipShipmentRepository->save($dropship);
            $this->dropshipShipmentService->createDropshipItem($dropship, $shipment);

            /** update prepare to ship */
            $this->dropshipRequestService->updatePrepareShipQty($shipment);

            /** update dropship request (status) */
            $this->dropshipRequestService->updateDropshipRequest($this->getRequest()->getParam('id'));

            /** return qty to catalog product */
//            $this->dropshipRequestService->returnQtyToCatalogProduct($shipment);

            $dropshipRequest = $this->dropshipRequestRepository->getById($this->getRequest()->getParam('id'));
            /** update supplier and shipment */
            $this->dropshipRequestService->updateSupplierShipment($dropshipRequest, $shipment);
            
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->messageManager->addErrorMessage(__('Cannot save dropship shipment.'));
        }
        return $resultRedirect->setPath('dropshipsuccess/dropshiprequest/edit', ['_current' => true]);
    }

    /**
     * Create magento shipment.
     * 
     * @param array $data
     * @return \Magento\Sales\Model\Order\Shipment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createShipment($data = [])
    {
        $shipmentLoader = $this->shipmentLoader;
        $shipmentLoader->setOrderId($data['order_id']);
        $shipmentLoader->setShipmentId(null);
        $shipmentLoader->setShipment($data['shipment']);
        $shipment = $shipmentLoader->load();
        if (!$shipment) {
            $this->_forward('noroute');
            return;
        }

        $shipment->register();
        $shipment->getOrder()->setCustomerNoteNotify(true);
        $this->_saveShipment($shipment);
        $this->shipmentSender->send($shipment);
        
        $this->messageManager->addSuccessMessage(
            __('The shipment has been created.')
        );
        return $shipment;
    }
}
