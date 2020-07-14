<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest\Packaging;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magestore\FulfilSuccess\Model\PackRequest\PackRequest;

class Save extends \Magento\Backend\App\Action
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
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface
     */
    protected $packRequestRepository;

    /**
     * @var \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService
     */
    protected $packRequestService;

    /**
     * @var \Magestore\FulfilSuccess\Service\Package\PackageService
     */
    protected $packageService;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param ShipmentSender $shipmentSender
     * @param \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface $packRequestRepository
     * @param \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService
     * @param \Magestore\FulfilSuccess\Service\Package\PackageService $packageService
     */
    public function __construct(
        Action\Context $context,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        ShipmentSender $shipmentSender,
        \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface $packRequestRepository,
        \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService,
        \Magestore\FulfilSuccess\Service\Package\PackageService $packageService
    ) {
        $this->shipmentLoader = $shipmentLoader;
        $this->labelGenerator = $labelGenerator;
        $this->shipmentSender = $shipmentSender;
        $this->packRequestRepository = $packRequestRepository;
        $this->packRequestService = $packRequestService;
        $this->packageService = $packageService;
        parent::__construct($context);
    }

    /**
     * Save shipment and order in one transaction
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    public function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->_objectManager->create(
            'Magento\Framework\DB\Transaction'
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $packRequestId = $this->getRequest()->getParam('pack_request_id');
        $packRequest = $this->packRequestRepository->get($packRequestId);

        $data = $this->getRequest()->getParam('shipment');
        $responseAjax = new \Magento\Framework\DataObject();
        if (!empty($data['comment_text'])) {
            $this->_objectManager->get('Magento\Backend\Model\Session')->setCommentText($data['comment_text']);
        }

        try {
            $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
            $this->shipmentLoader->setShipment($data);
            $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
            $shipment = $this->shipmentLoader->load();
            if (!$shipment) {
                $responseAjax->setError(true);
                $responseAjax->setMessage("Can not create shipment");
                $this->getResponse()->representJson($responseAjax->toJson());
                return;
            }

            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );

                $shipment->setCustomerNote($data['comment_text']);
                $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }

            $shipment->register();

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];

            if ($isNeedCreateLabel) {
                $this->labelGenerator->create($shipment, $this->getRequest());
            }
            $this->_objectManager->get('Magento\Framework\Registry')->register('create_shipment_when_pack_order', true);
            $this->_saveShipment($shipment);

            /**
             * Generate packages
             */
            $this->packageService->createPackages($shipment, $this->getRequest(), $packRequest);

            /** update prepare to ship */
            $this->packageService->updatePrepareShipQty($shipment);

            /**
             * Update packed_qty in pack_request_item table
             */
            $changeQtys = $this->packRequestService->getDataToUpdatePackedQty($data['items']);
            $this->packRequestService->updatePackedQtys($packRequest, $changeQtys);

            /**
             * Update status of pack_request table's record
             */
            if ($this->packRequestService->isPackRequestCompleted($packRequest)) {
                $this->packRequestService->complete($packRequest);
                $responseAjax->setData('close_view_detail', true);
            } else {
                $this->packRequestService->packPartially($packRequest);
            }

            if (!empty($data['send_email'])) {
                $this->shipmentSender->send($shipment);
            }

            $shipmentCreatedMessage = __('The shipment has been created.');
            $labelCreatedMessage = __('You created the shipping label.');

            $successMessage = $isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage : $shipmentCreatedMessage;

            $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);

            $responseAjax->setOk(true);
            $responseAjax->setMessage($successMessage);
            $responseAjax->setData('shipment_id', $shipment->getEntityId());
            $this->messageManager->addSuccessMessage($successMessage);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $responseAjax->setError(true);
            $responseAjax->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $responseAjax->setError(true);
            $responseAjax->setMessage(__('An error occurred while creating shipping label.'));
        }

        $this->getResponse()->representJson($responseAjax->toJson());
    }
}
