<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Observer\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\FulfilSuccess\Service\PickRequest\BuilderService;

/**
 * Observer SalesPlaceOrderAfter
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SalesPlaceOrderAfter implements ObserverInterface
{
    /**
     * @var BuilderService
     */
    protected $builderService;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SalesPlaceOrderAfter constructor.
     *
     * @param BuilderService $builderService
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        BuilderService $builderService,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->builderService = $builderService;
        $this->fulfilManagement = $fulfilManagement;
        $this->eventManager = $eventManager;
        $this->shipmentFactory = $shipmentFactory;
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * Process pick request from Sales Success
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();

        // Get fully order data
        $orderId = $order->getId();
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->objectManager->get(\Magento\Framework\Api\SearchCriteriaInterface::class);
        /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
        $filterGroup = $this->objectManager->get(\Magento\Framework\Api\Search\FilterGroup::class);
        /** @var \Magento\Framework\Api\Filter $filter */
        $filter = $this->objectManager->get(\Magento\Framework\Api\Filter::class);
        $filter->setField('entity_id');
        $filter->setValue($orderId);

        $filterGroup->setFilters([$filter]);
        $searchCriteria->setFilterGroups([$filterGroup]);

        $listOrder = $this->orderRepository->getList($searchCriteria);
        if ($listOrder->getTotalCount()) {
            $order = $listOrder->getItems()[$orderId];
        }

        if ($this->checkPicking($order)) {
            $pickData = $this->preparePickingData($order);
            if ($pickData) {
                $shipData = new \Magento\Framework\DataObject($pickData);
                $this->builderService->createPickRequestsFromPostData($shipData);
            }
        }
        return $this;
    }

    /**
     * Prepare picking data from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array|bool
     */
    public function preparePickingData($order)
    {
        $orderId = $order->getId();
        $items = $order->getAllItems();
        $shipItems = $this->getListShippingItem($order);
        if (count($items)) {
            $itemData = [];
            foreach ($items as $item) {
                if (!isset($shipItems[$item->getId()])) {
                    continue;
                }
                $itemTmp['qty'] = $shipItems[$item->getId()];
                $itemTmp['resource'] = $this->getResourcePickingItem($order);
                $itemTmp['order_item_id'] = $item->getId();
                $itemData[$item->getId()] = $itemTmp;
            }
            $packages = [
                [
                    'params' => ['container' => 'fulfil'],
                    'items' => $itemData
                ]
            ];
            $pickingData = [
                'order_id' => $orderId,
                'packages' => $packages
            ];
            return $pickingData;
        }
        return false;
    }

    /**
     * Retrieve shipment model instance
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getListShippingItem($order)
    {
        $itemCollection = $this->getShipment($order)->getAllItems();

        $dataItem = [];
        /** @var \Magento\Sales\Api\Data\ShipmentItemInterface $item */
        foreach ($itemCollection as $item) {
            $dataItem[$item->getOrderItemId()] = $item->getQty();
        }

        return $dataItem;
    }

    /**
     * Retrieve shipment model instance
     *
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment($order)
    {
        return $this->shipmentFactory->create($order, $this->getOrderItemData($order), []);
    }

    /**
     * Get Order Item Data
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getOrderItemData(\Magento\Sales\Model\Order $order)
    {
        $data = [];
        foreach ($order->getAllItems() as $item) {
            $data[$item->getId()] = $item->getQtyToShip();
        }
        return $data;
    }

    /**
     * Check Picking
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function checkPicking($order)
    {
        if ($order->getData('pos_id')
            && !$order->getData('pos_fulfill_online')
            && $order->getShippingMethod() != 'webpos_shipping_storepickup') {
            return true;
        }
        return false;
    }

    /**
     * Get Resource Picking Item
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string|int
     */
    public function getResourcePickingItem($order)
    {
        $posLocationId = $order->getPosLocationId();
        $pickingData = new \Magento\Framework\DataObject(
            ['resource' => $posLocationId, 'pos_location_id' => $posLocationId]
        );
        if ($this->fulfilManagement->isMSIEnable()) {
            $this->eventManager->dispatch(
                'fulfilsuccess_get_resource_picking_item_after_place_order',
                ['picking_data' => $pickingData]
            );
        }
        return $pickingData->getResource();
    }
}
