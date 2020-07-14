<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Exception;
use Magento\CatalogInventory\Api\StockManagementInterface;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magestore\DropshipSuccess\Service\DropshipRequest\DataProcessService;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magestore\DropshipSuccess;
use Magestore\DropshipSuccess\Model\DropshipRequestFactory;
use Magestore\DropshipSuccess\Service\DropshipRequest\DropshipRequestItemService;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;
use Magestore\DropshipSuccess\Api\DropshipRequestItemRepositoryInterface;
use Magestore\DropshipSuccess\Api\Data\DropshipRequestItemInterface;
use Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface;
use Magestore\OrderSuccess\Api\Data\OrderItemInterface as OrderSuccessOrderItemInterface;
use Magento\Sales\Model\Order\Shipment;
use Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipmentFactory;
use Magestore\SupplierSuccess\Api\Data\SupplierInterface;
use Magestore\SupplierSuccess\Model\Repository\SupplierRepository;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class DropshipRequestService
 * @package Magestore\DropshipSuccess\Service
 */
class DropshipRequestService
{

    /**
     * @var DataProcessService
     */
    protected $dataProcessService;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var DropshipRequestFactory
     */
    protected $dropshipRequestFactory;

    /**
     * @var DropshipRequestItemService
     */
    protected $dropshipRequestItemService;

    /**
     * @var DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepositoryInterface;

    /**
     * @var DropshipRequestItemRepositoryInterface
     */
    protected $dropshipRequestItemRepositoryInerface;

    /**
     * @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\CollectionFactory
     */
    protected $dropshipRequestCollectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepositoryInterface;

    /**
     * @var ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\CollectionFactory
     */
    protected $dropshipRequestItemCollectionFactory;

    /**
     * @var \Magestore\DropshipSuccess\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepositoryInterface;


    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemcollectionFactory;

    /**
     * @var DropshipSuccess\Api\DropshipRequestItemRepositoryInterface
     */
    protected $dropshipRequestItemRepository;

    /**
     * @var DropshipShipmentFactory
     */
    protected $dropshipShipmentFactory;

    /**
     * @var DropshipSuccess\Api\DropshipShipmentRepositoryInterface
     */
    protected $dropshipShipmentRepository;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory
     */
    protected $trackCollectionFactory;

    /**
     * @var DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\CollectionFactory
     */
    protected $dropshipShipmentCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var \Magestore\DropshipSuccess\Service\EmailService
     */
    protected $emailService;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlHelper;

    /**
     * @var SupplierRepository
     */
    protected $supplierRepository;

    /**
     * @var DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface
     */
    protected $dropshipSupplierShipmentInterface;

    /**
     * @var DropshipSuccess\Api\DropshipSupplierShipmentRepositoryInterface
     */
    protected $dropshipSupplierShipmentRepositoryInterface;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;
    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * DropshipRequestService constructor.
     * @param DataProcessService $dataProcessService
     * @param OrderRepositoryInterface $orderRepository
     * @param DropshipRequestFactory $dropshipRequestFactory
     * @param DropshipRequestItemService $dropshipRequestItemService
     * @param DropshipRequestRepositoryInterface $dropshipRequestRepositoryInterface
     * @param DropshipRequestItemRepositoryInterface $dropshipRequestItemRepositoryInerface
     * @param DropshipSuccess\Model\ResourceModel\DropshipRequest\CollectionFactory $dropshipRequestCollectionFactory
     * @param DropshipSuccess\Api\OrderItemRepositoryInterface $orderItemRepositoryInterface
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param ShipmentFactory $shipmentFactory
     * @param DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\CollectionFactory $dropshipRequestItemCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemcollectionFactory
     * @param DropshipRequestItemRepositoryInterface $dropshipRequestItemRepository
     * @param DropshipShipmentFactory $dropshipShipmentFactory
     * @param DropshipSuccess\Api\DropshipShipmentRepositoryInterface $dropshipShipmentRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\CollectionFactory $dropshipShipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param EmailService $emailService
     * @param ProductRepositoryInterface $productRepository
     * @param StockManagementInterface $stockManagement
     * @param \Magento\Framework\Url $urlHelper
     * @param SupplierRepository $supplierRepository
     * @param DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface $dropshipSupplierShipmentInterface
     * @param DropshipSuccess\Api\DropshipSupplierShipmentRepositoryInterface $dropshipSupplierShipmentRepositoryInterface
     * @param StoreRepositoryInterface $storeRepository
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     */
    public function __construct(
        DataProcessService $dataProcessService,
        OrderRepositoryInterface $orderRepository,
        DropshipRequestFactory $dropshipRequestFactory,
        DropshipRequestItemService $dropshipRequestItemService,
        DropshipRequestRepositoryInterface $dropshipRequestRepositoryInterface,
        DropshipRequestItemRepositoryInterface $dropshipRequestItemRepositoryInerface,
        \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\CollectionFactory $dropshipRequestCollectionFactory,
        \Magestore\DropshipSuccess\Api\OrderItemRepositoryInterface $orderItemRepositoryInterface,
        OrderRepositoryInterface $orderRepositoryInterface,
        ShipmentFactory $shipmentFactory,
        \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\CollectionFactory $dropshipRequestItemCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemcollectionFactory,
        \Magestore\DropshipSuccess\Api\DropshipRequestItemRepositoryInterface $dropshipRequestItemRepository,
        DropshipShipmentFactory $dropshipShipmentFactory,
        DropshipSuccess\Api\DropshipShipmentRepositoryInterface $dropshipShipmentRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\CollectionFactory $dropshipShipmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magestore\DropshipSuccess\Service\EmailService $emailService,
        ProductRepositoryInterface $productRepository,
        StockManagementInterface $stockManagement,
        \Magento\Framework\Url $urlHelper,
        SupplierRepository $supplierRepository,
        DropshipSuccess\Api\Data\DropshipSupplierShipmentInterface $dropshipSupplierShipmentInterface,
        DropshipSuccess\Api\DropshipSupplierShipmentRepositoryInterface $dropshipSupplierShipmentRepositoryInterface,
        StoreRepositoryInterface $storeRepository,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->dataProcessService = $dataProcessService;
        $this->orderRepository = $orderRepository;
        $this->dropshipRequestFactory = $dropshipRequestFactory;
        $this->dropshipRequestItemService = $dropshipRequestItemService;
        $this->dropshipRequestRepositoryInterface = $dropshipRequestRepositoryInterface;
        $this->dropshipRequestItemRepositoryInerface = $dropshipRequestItemRepositoryInerface;
        $this->dropshipRequestItemRepository = $dropshipRequestItemRepository;
        $this->dropshipShipmentFactory = $dropshipShipmentFactory;
        $this->dropshipShipmentRepository = $dropshipShipmentRepository;
        $this->dropshipRequestCollectionFactory = $dropshipRequestCollectionFactory;
        $this->orderItemRepositoryInterface = $orderItemRepositoryInterface;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->shipmentFactory = $shipmentFactory;
        $this->dropshipRequestItemCollectionFactory = $dropshipRequestItemCollectionFactory;
        $this->orderItemcollectionFactory = $orderItemcollectionFactory;
        $this->coreRegistry = $registry;
        $this->trackCollectionFactory = $trackCollectionFactory;
        $this->dropshipShipmentCollectionFactory = $dropshipShipmentCollectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->emailService = $emailService;
        $this->productRepository = $productRepository;
        $this->stockManagement = $stockManagement;
        $this->urlHelper = $urlHelper;
        $this->supplierRepository = $supplierRepository;
        $this->dropshipSupplierShipmentInterface = $dropshipSupplierShipmentInterface;
        $this->dropshipSupplierShipmentRepositoryInterface = $dropshipSupplierShipmentRepositoryInterface;
        $this->storeRepository = $storeRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param $data
     */
    public function createDropshipRequestsFromPostData($data)
    {
        $requestData = $this->dataProcessService->processPostedRequestData($data);
        if (!count($requestData[DataProcessService::REQUESTS])) {
            return;
        }
        $order = $this->orderRepository->get($requestData[DataProcessService::ORDER_ID]);
        foreach ($requestData[DataProcessService::REQUESTS] as $supplierId => $items) {
            $request = $this->createDropshipRequestFromOrder($supplierId, $order, $items);
            $requestUrl = $this->getUrlDropship($supplierId, $request->getId());
            $this->emailService->sendSubmitDropshipEmailToSupplier($request, $requestUrl);
        }
    }

    /**
     * Create PickRequest from Sales
     * $items[$itemId => $requestQty]
     *
     * @param int $warehouseId
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $items
     * @return PickRequestInterface
     */
    public function createDropshipRequestFromOrder($supplierId, $order, $items = [])
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->dropshipRequestFactory->create();
        $dropshipRequest->setOrderId($order->getId());
        $dropshipRequest->setOrderIncrementId($order->getIncrementId());
        $dropshipRequest->setSupplierId($supplierId);
        if ($supplierId > 0) {
            /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
            $supplier = \Magento\Framework\App\ObjectManager::getInstance()->create(
                'Magestore\SupplierSuccess\Api\SupplierRepositoryInterface'
            )->getById($supplierId);
            if ($supplier->getId()) {
                $dropshipRequest->setSupplierName($supplier->getSupplierName());
            }
        }
        $this->dropshipRequestRepositoryInterface->save($dropshipRequest);
        $totalItems = 0;
        foreach ($order->getItems() as $item) {
            if (count($items) && !isset($items[$item->getItemId()])) {
                /* do not add this item to Pick Request */
                continue;
            }
            /* prepare items to add to Pick Request */
            $requestQty = isset($items[$item->getItemId()]) ? floatval($items[$item->getItemId()]) : 0;
            /** @var \Magestore\DropshipSuccess\Model\DropshipRequest\Item $dropshipRequestItem */
            $dropshipRequestItem = $this->dropshipRequestItemService->addItemToDropshipRequest($dropshipRequest, $item, $requestQty);
            $this->dropshipRequestItemService->subtractQtyToShip($order, $item, $requestQty);
            $totalItems += $dropshipRequestItem->getQtyRequested();
        }

        $dropshipRequest->setTotalRequested($totalItems);
        $this->dropshipRequestRepositoryInterface->save($dropshipRequest);

        return $dropshipRequest;
    }

    /**
     * @param $supplierId
     * @return mixed
     */
    public function getDropshipRequestBySupplierId($supplierId)
    {
        return $this->dropshipRequestCollectionFactory->create()->getDropshipRequestBySupplierId($supplierId);
    }

    /**
     * Back dropship request to fulfil
     *
     * @param $requestId
     * @return mixed
     */
    public function backToPrepareFulfil($requestId)
    {
        $requestCancel = $this->dropshipRequestRepositoryInterface->getById($requestId);
        $request = $this->dropshipRequestRepositoryInterface->cancelDropshipRequest($requestCancel);
        $this->cancelRequestItems($requestId);
        $requestUrl = $this->getUrlDropship($request->getSupplierId(), $requestId);
        $this->emailService->sendCancelDropshipEmailToSupplier($request, $requestUrl);
    }

    /**
     * Cancel dropship request
     *
     * @param $requestId
     * @return mixed
     */
    public function cancel($request)
    {
        $this->dropshipRequestRepositoryInterface->cancelDropshipRequest($request);
        $this->cancelRequestItems($request->getId());
        $requestUrl = $this->getUrlDropship($request->getSupplierId(), $request->getId());
        $this->emailService->sendCancelDropshipEmailToSupplier($request, $requestUrl);
    }

    /**
     * Cancel dropship request items by request id
     *
     * @param $requestId
     * @return mixed
     */
    public function cancelRequestItems($requestId)
    {
        $requestItems = $this->dropshipRequestItemCollectionFactory->create();
        $requestItems->addFieldToFilter('dropship_request_id', $requestId);
        $orderItems = [];
        $order = null;
        foreach ($requestItems as $item) {
            $cancelQty = $this->dropshipRequestItemRepositoryInerface->cancelItemById($item->getId()) * -1;
            $this->orderItemRepositoryInterface->updateDropshipQty($item->getItemId(), $cancelQty);

            $curOrderItem = $this->orderItemRepository->get($item->getItemId());
            if(!$order) {
                $order = $this->orderRepository->get($curOrderItem->getOrderId());
            }
            if(!$orderItems) {
                $orderItems = $order->getItems();
            }
            foreach ($orderItems as $orderItem) {
                /* Get orderItem from order so we can call function getChildrenItems() */
                if ($orderItem->getItemId() == $curOrderItem->getItemId()) {
                    $this->dropshipRequestItemService->backQtyToShipOfCancelingItem($order, $orderItem, $cancelQty);
                    break;
                }
            }
        }
    }

    /**
     * get all item in dropship request
     * @param $dropshipRequestId
     * @return array
     */
    public function getDropshipItemsCollection($dropshipRequestId)
    {
        $items = [];
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->dropshipRequestRepositoryInterface->getById($dropshipRequestId);

        $orderId = $dropshipRequest->getOrderId();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepositoryInterface->get($orderId);

        /** @var \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory */
        $shipmentFactory = $this->shipmentFactory;
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $shipmentFactory->create(
            $order,
            $this->getOrderItemData($order),
            []
        );

        $shipmentItems = $shipment->getAllItems();
        /** @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection $dropshipRequestItemCollection */
        $dropshipRequestItemCollection = $this->dropshipRequestItemCollectionFactory->create();
        $dropshipRequestItem = $dropshipRequestItemCollection->addFieldToFilter('dropship_request_id', $dropshipRequestId);
        $pRItems = [];
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest\Item $pItem */
        foreach($dropshipRequestItem as $pItem) {
            if ($pItem->getQtyRequested() - $pItem->getQtyShipped() > 0) {
                $pRItems[$pItem->getItemId()] = $pItem->getData();
            }
        }
        $showItemIds = [];
        /** @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem */

        foreach ($shipmentItems as $shipmentItem) {
            if (isset($pRItems[$shipmentItem['order_item_id']])
                && !in_array($pRItems[$shipmentItem['order_item_id']]['item_id'], $showItemIds)
            ) {
                try {
                    $dropshipItem = $pRItems[$shipmentItem['order_item_id']];
                    if (!isset($dropshipItem['parent_item_id'])) {
                        $shipmentItem['qty'] = $dropshipItem['qty_requested'] - $dropshipItem['qty_shipped'];
                        $shipmentItem['item_id'] = $dropshipItem['item_id'];
                        $shipmentItem->addData($dropshipItem);
                        $items[] = $shipmentItem;
                        $showItemIds[] = $dropshipItem['item_id'];
                    } else {
                        /** @var \Magento\Sales\Model\Order\Item $orderItem */
                        $orderItem = $this->orderItemRepositoryInterface->get($dropshipItem['parent_item_id']);
                        if ($orderItem->isShipSeparately()) {
                            $shipmentItem['qty'] = $dropshipItem['qty_requested'] - $dropshipItem['qty_shipped'];
                            $shipmentItem['item_id'] = $dropshipItem['item_id'];
                            $shipmentItem->addData($dropshipItem);
                            $items[] = $shipmentItem;
                            $showItemIds[] = $dropshipItem['item_id'];
                        }
                        if (!$orderItem->isShipSeparately()
                            && !in_array($dropshipItem['parent_item_id'], $showItemIds)
                        ) {
                            $parentItemId = $dropshipItem['parent_item_id'];
                            if (!isset($pRItems[$parentItemId])) {
                                foreach ($shipmentItems as $shipmentParent) {
                                    if ($shipmentParent->getOrderItemId() == $parentItemId) {
                                        $shipmentItem = $shipmentParent;
                                        break;
                                    }
                                }
                                /** @var \Magento\Sales\Model\Order\Item $parentItem */
                                $parentItem = $this->orderItemRepositoryInterface->get($parentItemId);
                                $shipmentItem['qty'] = $dropshipItem['qty_requested'] - $dropshipItem['qty_shipped'];
                                $shipmentItem['order_item_id'] = $parentItemId;
                                $shipmentItem->addData($parentItem->getData());
                                $shipmentItem->addData($dropshipItem);
                                $shipmentItem['item_id'] = $parentItemId;
                                $shipmentItem['product_id'] = $parentItem->getProductId();
                                $shipmentItem['parent_item_id'] = null;
                                $shipmentItem['item_name'] = $parentItem->getName();
                                $shipmentItem['item_sku'] = $dropshipItem['item_sku'];
                                $shipmentItem['sku'] = $dropshipItem['item_sku'];
                                $items[] = $shipmentItem;
                                $showItemIds[] = $parentItemId;
                            }
                        }

                    }
                } catch (\Exception $e) {
                }
            }
        }
        return $items;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getOrderItemData(\Magento\Sales\Model\Order $order) {
        $data = [];
        foreach ($order->getAllItems() as $item) {
            $data[$item->getId()] = $item->getQtyToShip();
        }
        return $data;
    }

    /**
     * get qty to ship for children item with bundle and configuration product
     * @param $data
     * @return mixed
     */
    public function getDataToUpdateShippedQty($data)
    {
        $itemIds = array_keys($data);
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderItemCollection */
        $orderItemCollection = $this->orderItemcollectionFactory->create();
        $shippedItems = $orderItemCollection->addFieldToFilter('item_id', ['in' => $itemIds]);
        $shippedItemsIds = $shippedItems->getColumnValues('item_id');
        if (count($shippedItemsIds)) {
            //** @var \Magento\Sales\Model\ResourceModel\Sales\Item\Collection  $childrenItems */
            $childrenItems = $this->orderItemcollectionFactory->create()
                ->addFieldToFilter('parent_item_id', ['in' => $shippedItemsIds]);
            if ($childrenItems->getSize()) {
                /** @var \Magento\Sales\Model\Order\Item $childrenItem */
                foreach ($childrenItems as $childrenItem) {
                    $options = $childrenItem->getProductOptions();
                    $chilrendQty = 1;
                    if (isset($options['bundle_selection_attributes'])) {
                        $attribute = json_decode($options['bundle_selection_attributes'], true);
                        $chilrendQty = $attribute['qty'];
                    }
                    $parentId = $childrenItem->getParentItemId();
                    $data[$childrenItem->getItemId()] = $chilrendQty * $data[$parentId];
                }
            }
        }

        return $data;
    }

    /**
     * Update shipped qty for dropship request item
     * @param $dropshipRequestId
     * @param array $changeQtys
     */
    public function updateShippedQtys($dropshipRequestId, array $changeQtys)
    {
        /** @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection $dropshipRequestItemCollection */
        $dropshipRequestItemCollection = $this->dropshipRequestItemCollectionFactory->create();
        $dropshipRequestItemCollection
            ->addFieldToFilter(DropshipRequestItemInterface::DROPSHIP_REQUEST_ID, $dropshipRequestId)
            ->addFieldToFilter(DropshipRequestItemInterface::ITEM_ID, ['in' => array_keys($changeQtys)]);

        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest\Item $dropshipRequestItem */
        foreach ($dropshipRequestItemCollection as $dropshipRequestItem) {
            $dropshipRequestItem
                ->setQtyShipped($dropshipRequestItem->getQtyShipped() + $changeQtys[$dropshipRequestItem->getItemId()]);
            $this->dropshipRequestItemRepository->save($dropshipRequestItem);
        }
    }

    /**
     * update prepare qty for order item after create shipment
     * @param Shipment $shipment
     */
    public function updatePrepareShipQty(Shipment $shipment)
    {
        $moveItems = [];
        /** @var \Magento\Sales\Api\Data\ShipmentItemInterface[] $items */
        $items = $shipment->getItems();
        /** @var \Magento\Sales\Api\Data\ShipmentItemInterface  $item */
        foreach ($items as $item) {
            $moveItems[$item->getOrderItemId()] = [
                DropshipRequestItemInterface::ITEM_ID => $item->getOrderItemId(),
                'qty' => $item->getQty(),
                OrderSuccessOrderItemInterface::QTY_PREPARESHIP => -$item->getQty(),
            ];
        }
        if(count($moveItems)) {
            /* update dropship_qty of items in Sales Sales */
            $this->massUpdatePrepareShipQty($moveItems);
        }
    }

    /**
     * Mass Update PrepareShip Qty of Sales Items
     *
     * @param array $items
     */
    public function massUpdatePrepareShipQty($items)
    {
        foreach($items as $itemId => $qtyChange) {
            $qtyPrepareShipChange = isset($qtyChange[OrderSuccessOrderItemInterface::QTY_PREPARESHIP])
                ? $qtyChange[OrderSuccessOrderItemInterface::QTY_PREPARESHIP] : 0;

            if(!$qtyPrepareShipChange) {
                continue;
            }
            /** @var \Magento\Sales\Model\Order\Item $orderItem */
            $orderItem = $this->orderItemRepositoryInterface->get($itemId);

            /* ignore child of ship-together bundle item */
            if($orderItem->getParentItem()
                && $orderItem->getParentItem()->getProductType() != \Magento\Bundle\Model\Product\Type::TYPE_CODE
                && !$orderItem->isShipSeparately()) {
                continue;
            }

            if(!$orderItem->getSku()) {
                continue;
            }
            $qtyPrepareShip = $orderItem->getData(OrderSuccessOrderItemInterface::QTY_PREPARESHIP);
            $qtyPrepareShip = max(0, ($qtyPrepareShip + $qtyPrepareShipChange));
            $qtyPrepareShip = min($qtyPrepareShip , $orderItem->getQtyToShip());

            $orderItem->setData(OrderSuccessOrderItemInterface::QTY_PREPARESHIP, $qtyPrepareShip);

            $this->orderItemRepositoryInterface->save($orderItem);
        }
    }

    /**
     * Create Dropship Shipment
     * @param Shipment $shipment
     * @param $dropshipRequestId
     */
    public function createDropshipShipmentByShipment(Shipment $shipment, $dropshipRequestId)
    {
        try {
            /** @var \Magestore\DropshipSuccess\Model\DropshipRequest\DropshipShipment $dropshipShipment */
            $dropshipShipment = $this->dropshipShipmentFactory->create();
            $shipmentId = $shipment->getId();
            $dropshipShipment->setShipmentId($shipmentId);
            $dropshipShipment->setDropshipRequestId($dropshipRequestId);
            /** save dropship shipment */
            $this->dropshipShipmentRepository->save($dropshipShipment);
        } catch (\Exception $e) {

        }
    }

    /**
     * update dropship request status
     * @param $dropshipRequestId
     * @param null $status
     */
    public function updateDropshipRequest($dropshipRequestId, $status = null)
    {
        $dropshipRequest = $this->dropshipRequestRepositoryInterface->getById($dropshipRequestId);
        if (!$status) {
            $status = $this->getDropshipRequestStatus($dropshipRequest);
        }
        $dropshipRequest->setStatus($status);
        $this->dropshipRequestRepositoryInterface->save($dropshipRequest);
    }

    /**
     * get dropship request status
     * @param DropshipRequestInterface $dropshipRequest
     * @return int|null|string
     */
    public function getDropshipRequestStatus(DropshipRequestInterface $dropshipRequest)
    {
        if ($dropshipRequest->getStatus() == DropshipRequestInterface::STATUS_SHIPPED
            || $dropshipRequest->getStatus() == DropshipRequestInterface::STATUS_CANCELED) {
            return $dropshipRequest->getStatus();
        }
        /** @var DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection $dropshipRequestItemCollection */
        $dropshipRequestItemCollection = $this->dropshipRequestItemCollectionFactory->create();
        $dropshipRequestItemCollection->addFieldToFilter('dropship_request_id', $dropshipRequest->getId());
        /** @var DropshipSuccess\Model\DropshipRequest\Item $dropshipRequestItem */
        foreach ($dropshipRequestItemCollection as $dropshipRequestItem) {
            if ($dropshipRequestItem->getQtyRequested() != $dropshipRequestItem->getQtyShipped()) {
                return DropshipRequestInterface::STATUS_PARTIAL_SHIP;
            }
        }
        return DropshipRequestInterface::STATUS_SHIPPED;
    }

    /**
     * back qty product to catalog product
     * @param Shipment $shipment
     */
    public function returnQtyToCatalogProduct(Shipment $shipment)
    {
        $shipmentItem = $shipment->getAllItems();
        $orderItemIds = [];
        $shipmentItemQty = [];
        $scopeId = $this->storeRepository->getById($shipment->getStoreId())->getWebsiteId();
        /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
        foreach ($shipmentItem as $item) {
            $orderItemIds[] = $item->getOrderItemId();
            $shipmentItemQty[$item->getOrderItemId()] = $item->getQty();
        }
        $orderItems = $this->orderItemcollectionFactory->create()
            ->addFieldToFilter('item_id', ['in' => $orderItemIds]);
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        foreach ($orderItems as $orderItem) {
            $qty = $shipmentItemQty[$orderItem->getId()];
            if ($orderItem->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $parentId = $orderItem->getId();
                /** @var \Magento\Sales\Model\Order\Item $childrenItem */
                $childrenItem = \Magento\Framework\App\ObjectManager::getInstance()->create(
                    'Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory'
                )->create()
                    ->addFieldToFilter('parent_item_id', $parentId)
                    ->setCurPage(1)
                    ->setPageSize(1)
                    ->getFirstItem();
                if ($childrenItem->getId()) {
                    $productId = $childrenItem->getProductId();
                } else {
                    continue;
                }
            } else {
                $productId = $orderItem->getProductId();
            }
            if ($productId)
                $this->stockManagement->backItemQty($productId, $qty, $scopeId);
        }
    }

    /**
     * @return bool
     */
    public function isShippedRequest()
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->coreRegistry->registry('current_dropship_request');
        if ($dropshipRequest && $dropshipRequest->getId()) {
            if (in_array($dropshipRequest->getStatus(),
                [DropshipRequestInterface::STATUS_SHIPPED, DropshipRequestInterface::STATUS_CANCELED])) {
                return true;
            }
        }
        return false;
    }

    /**
     * get track carrier
     * @param $dropshipRequestId
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection|null
     */
    public function getTrackingCarriers($dropshipRequestId)
    {
        /** @var DropshipSuccess\Model\ResourceModel\DropshipRequest\DropshipShipment\Collection $dropshipShipmentCollection */
        $dropshipShipmentCollection = $this->dropshipShipmentCollectionFactory->create()
            ->addFieldToFilter('dropship_request_id', $dropshipRequestId);
        $shipmentIds = $dropshipShipmentCollection->getColumnValues('shipment_id');
        if (count($shipmentIds)) {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection $trackCollection */
            $trackCollection = $this->trackCollectionFactory->create()
                ->addFieldToFilter('parent_id', ['in' => $shipmentIds]);
            if ($trackCollection->getSize()) {
                return $trackCollection;
            }
        }
        return null;
    }

    /**
     * @param $dropshipRequestId
     * @return array
     */
    public function getDropshipItemsViewCollection($dropshipRequestId)
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->dropshipRequestRepositoryInterface->getById($dropshipRequestId);

        $orderId = $dropshipRequest->getOrderId();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepositoryInterface->get($orderId);

        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $shipmentCollection */
        $shipmentCollection = $this->shipmentCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId);
        $shipmentItems = [];
        /** @var \Magento\Sales\Model\Order\Shipment  $shipment */
        foreach ($shipmentCollection as $shipment) {
            $shipmentItems = array_merge($shipmentItems, $shipment->getAllItems());
        }

        /** @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection $dropshipRequestItemCollection */
        $dropshipRequestItemCollection = $this->dropshipRequestItemCollectionFactory->create();
        $dropshipRequestItem = $dropshipRequestItemCollection->addFieldToFilter('dropship_request_id', $dropshipRequestId);
        $pRItems = [];
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest\Item $pItem */
        $pItemIds = $dropshipRequestItem->getColumnValues('item_id');
        foreach($dropshipRequestItem as $pItem) {
            $pRItems[$pItem->getItemId()] = $pItem->getData();
            if ($pItem->getParentItemId() && !in_array($pItem->getParentItemId(), $pItemIds)) {
                $pRItems[$pItem->getParentItemId()] = $pItem->getData();
                $pRItems[$pItem->getParentItemId()]['parent_item_id'] = null;
            }
        }
        $showItemIds = [];
        /** @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem */
        $items = [];
        foreach ($shipmentItems as $shipmentItem) {
            if (isset($pRItems[$shipmentItem['order_item_id']])
                && !in_array($pRItems[$shipmentItem['order_item_id']]['item_id'], $showItemIds)
            ) {
                try {
                    $dropshipItem = $pRItems[$shipmentItem['order_item_id']];
                    if (!$dropshipItem['parent_item_id']) {
                        $shipmentItem['qty'] = $dropshipItem['qty_requested'] - $dropshipItem['qty_shipped'];
                        $shipmentItem['item_id'] = $dropshipItem['item_id'];
                        $shipmentItem->addData($dropshipItem);
                        $items[] = $shipmentItem;
                        $showItemIds[] = $dropshipItem['item_id'];
                    } else {
                        /** @var \Magento\Sales\Model\Order\Item $orderItem */
                        $orderItem = $this->orderItemRepositoryInterface->get($dropshipItem['parent_item_id']);
                        if ($orderItem->isShipSeparately()) {
                            $shipmentItem['qty'] = $dropshipItem['qty_requested'] - $dropshipItem['qty_shipped'];
                            $shipmentItem['item_id'] = $dropshipItem['item_id'];
                            $shipmentItem->addData($dropshipItem);
                            $items[] = $shipmentItem;
                            $showItemIds[] = $dropshipItem['item_id'];
                        }
                        if (!$orderItem->isShipSeparately()
                            && !in_array($dropshipItem['parent_item_id'], $showItemIds)
                        ) {
                            $parentItemId = $dropshipItem['parent_item_id'];
                            if (!isset($pRItems[$parentItemId])) {
                                foreach ($shipmentItems as $shipmentParent) {
                                    if ($shipmentParent->getOrderItemId() == $parentItemId) {
                                        $shipmentItem = $shipmentParent;
                                        break;
                                    }
                                }
                                /** @var \Magento\Sales\Model\Order\Item $parentItem */
                                $parentItem = $this->orderItemRepository->get($parentItemId);
                                $shipmentItem['qty'] = $dropshipItem['qty_requested'] - $dropshipItem['qty_shipped'];
                                $shipmentItem['order_item_id'] = $parentItemId;
                                $shipmentItem->addData($parentItem->getData());
                                $shipmentItem->addData($dropshipItem);
                                $shipmentItem['item_id'] = $parentItemId;
                                $shipmentItem['product_id'] = $parentItem->getProductId();
                                $shipmentItem['parent_item_id'] = null;
                                $shipmentItem['item_name'] = $parentItem->getName();
                                $shipmentItem['item_sku'] = $dropshipItem['item_sku'];
                                $shipmentItem['sku'] = $dropshipItem['item_sku'];
                                $items[] = $shipmentItem;
                                $showItemIds[] = $parentItemId;
                            }
                        }

                    }
                } catch (\Exception $e) {

                }
            }
        }
        return $items;
    }

    /**
     * get dropship url to auto login
     * @param $supplierId
     * @param $dropshipRequestId
     * @return string
     */
    public function getUrlDropship($supplierId, $dropshipRequestId)
    {
        $param = '';
        if ($supplierId) {
            $param .= 'supplier_id='.$supplierId;
        }
        if ($dropshipRequestId) {
            $param .= '&dropship_id='.$dropshipRequestId;
        }
        $param = base64_encode($param);
        $url = $this->urlHelper->getUrl('dropship/supplier/redirect',['dropship' => $param]);
        return $url;
    }

    /**
     * @param $data
     * @return array
     */
    public function decodeUrlDropship($data)
    {
        $returnData = [];
        $returnData['supplier_id'] = null;
        $returnData['dropship_id'] = null;
        $data = base64_decode($data);
        $data = explode('&', $data);
        $supplier = $data[0];
        $dropshipRequest = isset($data[1]) ? $data[1] : null;
        $supplier = explode('=', $supplier);
        if (isset($supplier[1])) {
            $returnData['supplier_id'] = $supplier[1];
        }
        if ($dropshipRequest) {
            $dropshipRequest = explode('=', $dropshipRequest);
            if (isset($dropshipRequest[1])) {
                $returnData['dropship_id'] = $dropshipRequest[1];
            }
        }
        return $returnData;
    }

    /**
     * update supplier and shipment
     * @param DropshipRequestInterface $dropshipRequest
     * @param Shipment $shipment
     */
    public function updateSupplierShipment(DropshipRequestInterface $dropshipRequest, Shipment $shipment)
    {
        try {
            $supplierId = $dropshipRequest->getSupplierId();
            /** @var SupplierInterface $supplier */
            $supplier = $this->supplierRepository->getById($supplierId);
            $dropshipSupplierShipment = $this->dropshipSupplierShipmentInterface;
            $dropshipSupplierShipment->setShipmentId($shipment->getId());
            $dropshipSupplierShipment->setSupplierId($supplierId);
            $dropshipSupplierShipment->setSupplierName($supplier->getSupplierName());
            $dropshipSupplierShipment->setSupplierCode($supplier->getSupplierCode());
            $this->dropshipSupplierShipmentRepositoryInterface->save($dropshipSupplierShipment);
        } catch (\Exception $e) {

        }
    }

    /**
     * forgot password url
     * @param SupplierInterface $supplier
     * @return string
     */
    public function getForgotPasswordUrl(SupplierInterface $supplier)
    {
        $param = 'supplier_id='. $supplier->getId();
        $param .= '&supplier_code='. $supplier->getSupplierCode();
        $param = base64_encode($param);
        $url = $this->urlHelper->getUrl('dropship/supplier/createPassword',['forgot' => $param]);
        return $url;
    }

    /**
     * @param $data
     * @return array
     */
    public function decodeForgotPasswordUrl($data)
    {
        $returnData = [];
        $returnData['supplier_id'] = null;
        $returnData['supplier_code'] = null;
        $data = base64_decode($data);
        $data = explode('&', $data);
        $supplierId = $data[0];
        $supplierCode = isset($data[1]) ? $data[1] : null;
        $supplierId = explode('=', $supplierId);
        if (isset($supplierId[1])) {
            $returnData['supplier_id'] = $supplierId[1];
        }
        if ($supplierCode) {
            $supplierCode = explode('=', $supplierCode);
            if (isset($supplierCode[1])) {
                $returnData['supplier_code'] = $supplierCode[1];
            }
        }
        return $returnData;
    }
}
