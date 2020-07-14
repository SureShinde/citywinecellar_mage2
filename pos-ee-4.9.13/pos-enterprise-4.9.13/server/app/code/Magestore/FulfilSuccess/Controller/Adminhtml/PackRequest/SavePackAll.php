<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\Package\PackageService;
use Magestore\FulfilSuccess\Service\PackRequest\PackRequestService;

class SavePackAll extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::pack_request';
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var PackRequestRepositoryInterface
     */
    protected $packRequestRepository;

    /**
     * @var PackRequestService
     */
    protected $packRequestService;

    /**
     * @var PackageService
     */
    protected $packageService;

    /**
    protected $packRequestRepository;
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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PackRequestRepositoryInterface $packRequestRepository,
        PackRequestService $packRequestService,
        PackageService $packageService
    ) {
        parent::__construct($context, $shipmentLoader, $labelGenerator, $shipmentSender);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->packRequestRepository = $packRequestRepository;
        $this->packRequestService = $packRequestService;
        $this->packageService = $packageService;
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
            return $resultRedirect->setPath('fulfilsuccess/packRequest/index', ['_current' => true]);
        }

        $data = $this->getRequest()->getParams();
        $data['shipment']['items'] = [];
        $items = $this->packRequestRepository->getItemList($data['pack_request_id']);
        foreach ($items as $item) {
            if (($item->getRequestQty() - $item->getPackedQty()) > 0) {
                $data['shipment']['items'][$item->getItemId()] = $item->getRequestQty() - $item->getPackedQty();
            }
        }
        if (isset($data['shipment']['items']) && count($data['shipment']['items'])) {
            $data['total_shipped'] = array_sum($data['shipment']['items']);
        }

        $carrier = $this->getRequest()->getPost('tracking_carrier');
        $number = $this->getRequest()->getPost('tracking_number');
        $title = $this->getRequest()->getPost('tracking_title');
        try {
            if (empty($data['total_shipped']) || floatval($data['total_shipped']) <= 0) {
                $response = [
                    'error' => true,
                    'message' => __('Please enter qty to pack.'),
                ];
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }

            if (empty($carrier)) {
                $response = [
                    'error' => true,
                    'message' => __('Please specify a carrier.'),
                ];
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
            if (empty($number)) {
                $response = [
                    'error' => true,
                    'message' => __('Please enter a tracking number.'),
                ];
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
            $shipmentLoader = $this->shipmentLoader;
            $shipmentLoader->setOrderId($data['order_id']);
            $shipmentLoader->setShipmentId(null);
            $shipmentLoader->setShipment($data['shipment']);
            $trackingData = [];
            $trackingData[1] = [
                'carrier_code' => $carrier,
                'title' => $title,
                'number' => $number
            ];
            $shipmentLoader->setTracking($trackingData);
            $shipment = $shipmentLoader->load();
            if ($shipment) {
                /** create sales shipment track */
                $shipment->register();
                $this->_objectManager->get('Magento\Framework\Registry')->register('create_shipment_when_pack_order', true);
                $this->_saveShipment($shipment);
                $tracking = $shipment->getTracksCollection()->setCurPage(1)->setPageSize(1)->getFirstItem();
                if ($tracking->getId()) {
                    $trackId = $tracking->getId();
                    $packRequestId = $data['pack_request_id'];

                    $packRequest = $this->packRequestRepository->get($packRequestId);
                    /** update packed qty to pack request */
                    $this->packRequestService->updatePackedQtys($packRequest, $data['shipment']['items']);

                    /** create package and package item */
                    $this->packageService->createPackageByShipment($shipment, $packRequest, $trackId);

                    /** update pack request status */
                    if ($this->packRequestService->isPackRequestCompleted($packRequest)) {
                        $this->packRequestService->complete($packRequest);
                    }

                    $this->messageManager->addSuccessMessage(__('Packing has been created!'));
                    if ($packRequest->getStatus() == PackRequestInterface::STATUS_PACKED) {
                        return $this->getResponse()->setBody('completed');
                    }
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => __('We can\'t create packing'),
                ];
            }
            return $resultRedirect->setPath('fulfilsuccess/packRequest/getInfo', ['_current' => true, 'pack_request_id' => $data['pack_request_id']]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Can not create packing. Please try again!')];
        }
        if (is_array($response)) {
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
        return $resultRedirect->setPath('fulfilsuccess/packRequest/index', ['_current' => true]);
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
            __('The packing has been created.')
        );
        return $shipment;
    }

    /**
     * Validate post data
     *
     * @param array $postData
     * @return array
     */
    public function validateShipmentData($postData = []){
        $postData['total_shipped'] = array_sum($postData['shipment']['items']);

        return $postData;
    }
}
