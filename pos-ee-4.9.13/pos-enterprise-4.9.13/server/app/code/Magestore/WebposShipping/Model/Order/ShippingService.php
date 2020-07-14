<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposShipping\Model\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magestore\Webpos\Api\Data\Checkout\Order\ItemInterface;
use Magestore\Webpos\Api\Data\Checkout\OrderInterface;
use Magestore\Webpos\Model\Checkout\PosOrder;
use Magestore\Webpos\Model\Request\Actions\ShipmentAction;
use Magestore\WebposShipping\Api\Order\ShippingServiceInterface;
use Magento\Sales\Api\Data\ShipmentItemCreationInterface;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use Magestore\FulfilSuccess\Service\PackRequest\PackRequestService;
use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestService;
use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;

/**
 * Class ShippingService
 *
 * @package Magestore\WebposShipping\Model\Order
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShippingService implements ShippingServiceInterface
{
    /**
     * @var \Magestore\Webpos\Model\Sales\OrderRepository
     */
    protected $posOrderRepository;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Sales\Api\ShipOrderInterface
     */
    protected $shipOrder;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magestore\Webpos\Helper\Order
     */
    protected $posOrderHelper;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var ServiceInputProcessor
     */
    protected $serviceInputProcessor;
    /**
     * @var \Magestore\Webpos\Model\Request\ActionLogFactory
     */
    protected $actionLogFactory;
    /**
     * @var \Magestore\Webpos\Log\Logger
     */
    protected $logger;

    /**
     * ShippingService constructor.
     *
     * @param \Magestore\Webpos\Model\Sales\OrderRepository $posOrderRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\ShipOrderInterface $shipOrder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Helper\Order $posOrderHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param ServiceInputProcessor $serviceInputProcessor
     * @param \Magestore\Webpos\Model\Request\ActionLogFactory $actionLogFactory
     * @param \Magestore\Webpos\Log\Logger $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magestore\Webpos\Model\Sales\OrderRepository $posOrderRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\ShipOrderInterface $shipOrder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Helper\Order $posOrderHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\RequestInterface $request,
        ServiceInputProcessor $serviceInputProcessor,
        \Magestore\Webpos\Model\Request\ActionLogFactory $actionLogFactory,
        \Magestore\Webpos\Log\Logger $logger
    ) {
        $this->posOrderRepository = $posOrderRepository;
        $this->orderRepository = $orderRepository;
        $this->shipOrder = $shipOrder;
        $this->objectManager = $objectManager;
        $this->posOrderHelper = $posOrderHelper;
        $this->moduleManager = $moduleManager;
        $this->request = $request;
        $this->serviceInputProcessor = $serviceInputProcessor;
        $this->actionLogFactory = $actionLogFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function tryCancelPickPackRequest($order_id)
    {
        // cancel all pick/pack request if exist
        if ($this->moduleManager->isEnabled('Magestore_FulfilSuccess')) {
            /** @var OrderInterface $order */
            $order = $this->posOrderRepository->get($order_id);
            $this->movePickRequestToNeedToShip($order->getIncrementId());
            $this->movePackRequestToNeedToShip($order_id);
        }
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createShipment(
        $requestIncrementId,
        $order_increment_id,
        $items_to_ship = [],
        $note = null,
        $tracks = null
    ) {
        try {
            /** @var \Magestore\Webpos\Api\Data\Checkout\OrderInterface $existedOrder */
            $existedOrder = $this->posOrderRepository->getWebposOrderByIncrementId($order_increment_id);
        } catch (\Exception $e) {
            $existedOrder = false;
        }
        if (!$existedOrder || !$existedOrder->getEntityId()) {
            throw new LocalizedException(
                __('The order that you want to create shipment has not been converted successfully!'),
                new \Exception(),
                \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_SAVED_REQUEST_TO_SERVER
            );
        }

        try {
            $result = $this->processCreateShipmentActionLog($requestIncrementId);

            if (!$result) {
                throw new LocalizedException(
                    __('Some things went wrong when trying to create shipment!'),
                    new \Exception(),
                    \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_SAVED_REQUEST_TO_SERVER
                );
            }

            return $result;
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Some things went wrong when trying to create shipment!'),
                new \Exception(),
                \Magestore\Appadmin\Api\Event\DispatchServiceInterface::EXCEPTION_CODE_SAVED_REQUEST_TO_SERVER
            );
        }
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function processCreateShipmentActionLog($requestIncrementId)
    {
        /** @var \Magestore\Webpos\Model\Request\ActionLog $actionLog */
        $actionLog = $this->actionLogFactory->create();
        $actionLog->load($requestIncrementId, 'request_increment_id');
        if (!$actionLog->getId() ||
            $actionLog->getActionType() != ShipmentAction::ACTION_TYPE ||
            $actionLog->getStatus() == \Magestore\Webpos\Model\Request\ActionLog::STATUS_COMPLETED) {
            return false;
        }

        // Modify request params
        $requestParams = $this->request->getParams();
        $requestLocationId = $actionLog->getLocationId();
        $requestParams[PosOrder::PARAM_ORDER_LOCATION_ID] = $requestLocationId;
        $this->request->setParams($requestParams);
        // End: Modify request params

        // Convert array to object parameter
        $params = json_decode($actionLog->getParams(), true);
        $params = $this->serviceInputProcessor->process(
            \Magestore\WebposShipping\Api\Order\ShippingServiceInterface::class,
            'createShipment',
            $params
        );
        $order_increment_id = $params[1];
        $items_to_ship = $params[2];
        $note = $params[3];
        // End: Convert array to object parameter

        ////////////////////////////////
        /// Process Take Payment
        ////////////////////////////////
        try {
            /** @var \Magestore\Webpos\Api\Data\Checkout\OrderInterface $existedOrder */
            $existedOrder = $this->posOrderRepository->getWebposOrderByIncrementId($order_increment_id);
            $this->tryCancelPickPackRequest($existedOrder->getEntityId());

            $order = $this->orderRepository->get($existedOrder->getEntityId());

            /* Shipment items */
            $itemsToShip = [];
            if (!empty($items_to_ship)) {
                /** @var ItemInterface[] $orderItems */
                $orderItems = [];
                /** @var ItemInterface $item */
                foreach ($order->getItems() as $item) {
                    $itemId = $item->getTmpItemId() ? $item->getTmpItemId() : $item->getItemId();
                    $orderItems[$itemId] = $item;
                }
                foreach ($items_to_ship as $itemToShip) {
                    if (empty($orderItems[$itemToShip->getOrderItemId()])) {
                        continue;
                    }

                    /** @var ShipmentItemCreationInterface $shipmentItemCreation */
                    $shipmentItemCreation = $this->objectManager->create(ShipmentItemCreationInterface::class);
                    $shipmentItemCreation->setOrderItemId($orderItems[$itemToShip->getOrderItemId()]->getItemId());
                    $shipmentItemCreation->setQty($itemToShip->getQtyToShip());
                    $itemsToShip[] = $shipmentItemCreation;
                }
            }

            /* TODO: add tracks when create shipment */
            $tracks = [];

            /* Shipment comments */
            $shipmentComment = null;
            if ($note) {
                /** @var ShipmentCommentCreationInterface $shipmentComment */
                $shipmentComment = $this->objectManager->create(ShipmentCommentCreationInterface::class);
                $shipmentComment->setComment($note);
                $shipmentComment->setIsVisibleOnFront(true);
            }

            $this->shipOrder->execute(
                $existedOrder->getEntityId(),
                $itemsToShip,
                false,
                false,
                $shipmentComment,
                $tracks
            );

            $this->addNote(__("A shipment was created on POS"), $order);
            if ($note) {
                $this->addNote($note, $order);
            }

            $existedOrder = $this->posOrderRepository->getWebposOrderByIncrementId($order_increment_id);

            // Update action log
            $actionLog->setStatus(\Magestore\Webpos\Model\Request\ActionLog::STATUS_COMPLETED)->save();

            return $this->posOrderHelper->verifyOrderReturn($existedOrder);
        } catch (\Exception $e) {
            $this->logger->info($order_increment_id);
            $this->logger->info($e->getMessage());
            $this->logger->info($e->getTraceAsString());
            $this->logger->info('___________________________________________');
            // Update action log
            $actionLog->setStatus(\Magestore\Webpos\Model\Request\ActionLog::STATUS_FAILED)->save();
            return false;
        }
    }

    /**
     * Add comment to order
     *
     * @param string $note
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function addNote($note, $order)
    {
        $history = $order->addStatusHistoryComment($note, $order->getStatus());
        $history->setIsVisibleOnFront(true);
        $history->setIsCustomerNotified(0);
        $history->save();
    }

    /**
     * Cancel fulfill request
     *
     * @param int $orderIncrementId
     */
    public function movePickRequestToNeedToShip($orderIncrementId)
    {
        /**
         * @var PickRequestRepositoryInterface $pickRequestRepository
         */
        $pickRequestRepository = $this->objectManager->get(PickRequestRepositoryInterface::class);
        /** @var \Magestore\FulfilSuccess\Api\Data\PickRequestInterface $pickRequest */
        $pickRequest = $pickRequestRepository->getByOrderIncrementId($orderIncrementId);

        if (!$pickRequest->getId()) {
            return;
        }

        /** @var PickRequestService $pickRequestService */
        $pickRequestService = $this->objectManager->get(PickRequestService::class);
        $pickRequestService->moveItemsToNeedToShip($pickRequest);
    }

    /**
     * Cancel fulfill request
     *
     * @param int $orderId
     */
    public function movePackRequestToNeedToShip($orderId)
    {
        /**
         * @var PackRequestRepositoryInterface $packRequestRepository
         */
        $packRequestRepository = $this->objectManager->get(PackRequestRepositoryInterface::class);
        /** @var \Magestore\FulfilSuccess\Api\Data\PackRequestInterface[] $packRequest */
        $packRequests = $packRequestRepository->getByOrderId($orderId);

        if (empty($packRequests)) {
            return;
        }

        /** @var PackRequestService $packRequestService */
        $packRequestService = $this->objectManager->get(PackRequestService::class);

        /** @var \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest */
        foreach ($packRequests as $packRequest) {
            $packRequestService->moveItemsToNeedToShip($packRequest);
        }
    }
}
