<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PackRequest;

use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface;
use Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface;
use Magestore\FulfilSuccess\Api\PackRequestItemRepositoryInterface;
use Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface;
use Magestore\FulfilSuccess\Api\PickRequestItemRepositoryInterface;
use Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface;
use Magestore\FulfilSuccess\Service\Location\LocationServiceInterface;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestService;
use Magestore\FulfilSuccess\Service\PickRequest\BuilderService as PickRequestBuilder;
use Magestore\OrderSuccess\Api\OrderRepositoryInterface;
use Magestore\FulfilSuccess\Service\Locator\UserServiceInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magestore\OrderSuccess\Api\Data\OrderItemInterface as OrderSuccessOrderItemInterface;

class PackRequestService
{
    /**
     * @var PickRequestRepositoryInterface
     */
    protected $pickRequestRepository;

    /**
     * @var PickRequestItemRepositoryInterface
     */
    protected $pickRequestItemRepository;

    /**
     * @var PackRequestRepositoryInterface
     */
    protected $packRequestRepository;

    /**
     * @var PackRequestItemRepositoryInterface
     */
    protected $packRequestItemRepository;

    /**
     * @var ItemService
     */
    protected $itemService;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory
     */
    protected $packRequestItemCollectionFactory;

    /**
     * @var LocationServiceInterface
     */
    protected $locationService;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepositoryInterface;

    /**
     * @var ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Sales\Api\OrderItemRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $orderItemcollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface
     */
    protected $orderFulfilItemRepository;

    /**
     * @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory
     */
    protected $packageCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory
     */
    protected $trackCollectionFactory;

    /**
     * @var PickRequestBuilder
     */
    protected $pickRequestBuilder;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magestore\FulfilSuccess\Model\Db\QueryProcessorInterface
     */
    protected $queryProcessor;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * PackRequestService constructor.
     * @param PickRequestRepositoryInterface $pickRequestRepository
     * @param PickRequestItemRepositoryInterface $pickRequestItemRepository
     * @param PackRequestRepositoryInterface $packRequestRepository
     * @param PackRequestItemRepositoryInterface $packRequestItemRepository
     * @param ItemService $itemService
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory $packRequestItemCollectionFactory
     * @param LocationServiceInterface $locationService
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param ShipmentFactory $shipmentFactory
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemcollectionFactory
     * @param DateTime $dateTime
     * @param UserServiceInterface $userService
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param OrderItemRepositoryInterface $orderFulfilItemRepository
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory $packageCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param PickRequestBuilder $pickRequestBuilder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\FulfilSuccess\Model\Db\QueryProcessorInterface $queryProcessor
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     */
    public function __construct(
        PickRequestRepositoryInterface $pickRequestRepository,
        PickRequestItemRepositoryInterface $pickRequestItemRepository,
        PackRequestRepositoryInterface $packRequestRepository,
        PackRequestItemRepositoryInterface $packRequestItemRepository,
        ItemService $itemService,
        \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\CollectionFactory $packRequestItemCollectionFactory,
        LocationServiceInterface $locationService,
        OrderRepositoryInterface $orderRepositoryInterface,
        ShipmentFactory $shipmentFactory,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemcollectionFactory,
        DateTime $dateTime,
        UserServiceInterface $userService,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magestore\FulfilSuccess\Api\OrderItemRepositoryInterface $orderFulfilItemRepository,
        \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory $packageCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        PickRequestBuilder $pickRequestBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\FulfilSuccess\Model\Db\QueryProcessorInterface $queryProcessor,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
    )
    {
        $this->pickRequestRepository = $pickRequestRepository;
        $this->pickRequestItemRepository = $pickRequestItemRepository;
        $this->packRequestRepository = $packRequestRepository;
        $this->packRequestItemRepository = $packRequestItemRepository;
        $this->itemService = $itemService;
        $this->packRequestItemCollectionFactory = $packRequestItemCollectionFactory;
        $this->locationService = $locationService;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->shipmentFactory = $shipmentFactory;
        $this->dateTime = $dateTime;
        $this->userService = $userService;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemcollectionFactory = $orderItemcollectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->orderFulfilItemRepository = $orderFulfilItemRepository;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->trackCollectionFactory = $trackCollectionFactory;
        $this->pickRequestBuilder = $pickRequestBuilder;
        $this->objectManager = $objectManager;
        $this->queryProcessor = $queryProcessor;
        $this->fulfilManagement = $fulfilManagement;
    }

    public function updateRequestQtys(PackRequestInterface $packRequest, array $changeQtys)
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\Collection $packRequestItemCollection */
        $packRequestItemCollection = $this->packRequestItemCollectionFactory->create();
        $packRequestItemCollection
            ->addFieldToFilter(PackRequestItemInterface::PACK_REQUEST_ID, $packRequest->getId())
            ->addFieldToFilter(PackRequestItemInterface::ITEM_ID, ['in' => array_keys($changeQtys)]);

        foreach ($packRequestItemCollection as $packRequestItem) {
            /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequestItem $packRequestItem */
            $packRequestItem
                ->setRequestQty($packRequestItem->getPackedQty() + $changeQtys[$packRequestItem->getItemId()]);

            $this->packRequestItemRepository->save($packRequestItem);
        }
    }

    /**
     * Add Picked Item to Pack Request
     *
     * @param PackRequestInterface $packRequest
     * @param PickRequestItemInterface $pickItem
     * @return PackRequestInterface
     */
    public function addPickItemToPackRequest(PackRequestInterface $packRequest, PickRequestItemInterface $pickItem)
    {
        $packRequestItem = $this->packRequestRepository->getItem($packRequest, $pickItem->getItemId());
        if (!$packRequestItem->getPackRequestItemId()) {
            $packRequestItem->setItemId($pickItem->getItemId());
            $packRequestItem->setParentItemId($pickItem->getParentItemId());
            $packRequestItem->setItemName($pickItem->getItemName());
            $packRequestItem->setItemSku($pickItem->getItemSku());
            $packRequestItem->setItemBarcode($pickItem->getItemBarcode());
            $packRequestItem->setProductId($pickItem->getProductId());
            $packRequestItem->setRequestQty($pickItem->getPickedQty());
            $packRequestItem->setPackRequestId($packRequest->getPackRequestId());
        } else {
            $packRequestItem->setRequestQty($packRequestItem->getRequestQty() + $pickItem->getPickedQty());
        }
        $this->packRequestItemRepository->save($packRequestItem);

        return $packRequest;
    }


    /**
     * Update packed qty for pack request item
     *
     * @param PackRequestInterface $packRequest
     * @param array $changeQtys
     *
     * @return void
     */
    public function updatePackedQtys(PackRequestInterface $packRequest, array $changeQtys)
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\Collection $packRequestItemCollection */
        $packRequestItemCollection = $this->packRequestItemCollectionFactory->create();
        $packRequestItemCollection
            ->addFieldToFilter(PackRequestItemInterface::PACK_REQUEST_ID, $packRequest->getId())
            ->addFieldToFilter(PackRequestItemInterface::ITEM_ID, ['in' => array_keys($changeQtys)]);

        foreach ($packRequestItemCollection as $packRequestItem) {
            /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequestItem $packRequestItem */
            $packRequestItem
                ->setPackedQty($packRequestItem->getPackedQty() + $changeQtys[$packRequestItem->getItemId()]);

            $this->packRequestItemRepository->save($packRequestItem);
        }
    }

    /**
     * Check if pack request is completed
     *
     * @param PackRequestInterface $packRequest
     * @return bool
     */
    public function isPackRequestCompleted(PackRequestInterface $packRequest)
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\Collection $packRequestItemCollection */
        $packRequestItemCollection = $this->packRequestItemCollectionFactory->create();
        $packRequestItemCollection
            ->addFieldToFilter(PackRequestItemInterface::PACK_REQUEST_ID, $packRequest->getId());
        foreach ($packRequestItemCollection as $packRequestItem) {
            /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequestItem $packRequestItem */
            if ($packRequestItem->getRequestQty() != $packRequestItem->getPackedQty()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Complete a pack request
     *
     * @param PackRequestInterface $packRequest
     *
     * @return void
     */
    public function complete(PackRequestInterface $packRequest)
    {
        $packRequest->setAge($this->getAge($packRequest));
        $packRequest->setStatus(PackRequestInterface::STATUS_PACKED);
        $packRequest->setUserId($this->userService->getCurrentUserId());

        $this->packRequestRepository->save($packRequest);
    }

    /**
     * Save a pack request as partial because it's not fully packed
     *
     * @param PackRequestInterface $packRequest
     */
    public function packPartially(PackRequestInterface $packRequest)
    {
        if ($packRequest->getStatus() == PackRequestInterface::STATUS_PACKING) {
            $packRequest->setStatus(PackRequestInterface::STATUS_PARTIAL_PACK);
            $packRequest->setUserId($this->userService->getCurrentUserId());
            $this->packRequestRepository->save($packRequest);
        }
    }

    public function packAll(PackRequestInterface $packRequest)
    {
        $packRequest->setAge($this->getAge($packRequest));
        $packRequest->setStatus(PackRequestInterface::STATUS_PACKED);
        $packRequest->setUserId($this->userService->getCurrentUserId());
        $this->packRequestRepository->save($packRequest);
        /* create shipments here */

        /* */
        return $this;
    }

    /**
     * Cancel a pack request
     *
     * @param PickRequestInterface $packRequest
     */
    public function cancel(PackRequestInterface $packRequest)
    {

        if ($packRequest->getStatus() == PackRequestInterface::STATUS_PACKED) {
            return;
        }
        if ($packRequest->getStatus() == PackRequestInterface::STATUS_PARTIAL_PACK) {
            $packRequest->setStatus(PackRequestInterface::STATUS_PACKED);
        } else {
            $packRequest->setStatus(PackRequestInterface::STATUS_CANCELED);
        }
        $packRequest->setAge($this->getAge($packRequest));
        $this->packRequestRepository->save($packRequest);
    }

    /**
     * Return warehouse_id for packing staff
     */
    public function getWarehouseId()
    {
        return $this->locationService->getCurrentWarehouseId();
    }

    /**
     * Move items in PackRequest to Pick
     *
     * @param PackRequestInterface $packRequest
     * @return $this
     */
    public function moveItemsToPick(PackRequestInterface $packRequest)
    {
        /* create new pick request from Pack Request */
        $this->pickRequestBuilder->createPickRequestFromPack($packRequest);

        /* update status of Pack Request */
        if ($packRequest->getStatus() == PackRequestInterface::STATUS_PACKING) {
            $packRequest->setStatus(PackRequestInterface::STATUS_CANCELED);
        }
        if ($packRequest->getStatus() == PackRequestInterface::STATUS_PARTIAL_PACK) {
            $packRequest->setStatus(PackRequestInterface::STATUS_PACKED);
        }
        $this->packRequestRepository->save($packRequest);
        return $this;
    }

    /**
     * @param $packRequestId
     * @return array
     */
    public function getPackedItemsCollection($packRequestId)
    {
        $items = [];
        /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequest $packRequest */
        $packRequest = $this->packRequestRepository->get($packRequestId);

        $orderId = $packRequest->getOrderId();

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
        $this->verifyShipmentItems($shipmentItems, $order);
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\Collection $packRequestItemCollection */
        $packRequestItemCollection = $this->packRequestItemCollectionFactory->create();
        $packRequestItem = $packRequestItemCollection->addFieldToFilter('pack_request_id', $packRequestId);
        $pRItems = [];
        /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequestItem $pItem */
        foreach ($packRequestItem as $pItem) {
            if ($pItem->getRequestQty() - $pItem->getPackedQty() > 0) {
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
                    $packItem = $pRItems[$shipmentItem['order_item_id']];
                    if (!$packItem['parent_item_id']) {
                        $shipmentItem['qty'] = $packItem['request_qty'] - $packItem['packed_qty'];
                        $shipmentItem['item_id'] = $packItem['item_id'];
                        $shipmentItem->addData($packItem);
                        $items[] = $shipmentItem;
                        $showItemIds[] = $packItem['item_id'];
                    } else {
                        /** @var \Magento\Sales\Model\Order\Item $orderItem */
                        $orderItem = $this->orderItemRepository->get($packItem['parent_item_id']);
                        if ($orderItem->isShipSeparately()) {
                            $shipmentItem['qty'] = $packItem['request_qty'] - $packItem['packed_qty'];
                            $shipmentItem['item_id'] = $packItem['item_id'];
                            $shipmentItem->addData($packItem);
                            $items[] = $shipmentItem;
                            $showItemIds[] = $packItem['item_id'];
                        }
                        if (!$orderItem->isShipSeparately()
                            && !in_array($packItem['parent_item_id'], $showItemIds)
                        ) {
                            $parentItemId = $packItem['parent_item_id'];
                            if (!isset($pRItems[$parentItemId])) {
                                foreach ($shipmentItems as $shipmentParent) {
                                    if ($shipmentParent->getOrderItemId() == $parentItemId) {
                                        $shipmentItem = $shipmentParent;
                                        break;
                                    }
                                }
                                /** @var \Magento\Sales\Model\Order\Item $parentItem */
                                $parentItem = $this->orderItemRepository->get($parentItemId);
                                $shipmentItem['qty'] = $packItem['request_qty'] - $packItem['packed_qty'];
                                $shipmentItem['order_item_id'] = $parentItemId;
                                $shipmentItem->addData($parentItem->getData());
                                $shipmentItem->addData($packItem);
                                $shipmentItem['item_id'] = $parentItemId;
                                $shipmentItem['product_id'] = $parentItem->getProductId();
                                $shipmentItem['parent_item_id'] = null;
                                $shipmentItem['item_name'] = $parentItem->getName();
                                $shipmentItem['item_sku'] = $packItem['item_sku'];
                                $shipmentItem['sku'] = $packItem['item_sku'];
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
    public function getOrderItemData(\Magento\Sales\Model\Order $order)
    {
        $data = [];
        foreach ($order->getAllItems() as $item) {
            $data[$item->getId()] = $item->getQtyToShip();
        }
        return $data;
    }

    /**
     * @param array $shipmentItems
     * @param \Magento\Sales\Model\Order $order
     */
    public function verifyShipmentItems(&$shipmentItems, $order)
    {
        $childData = [];
        // get list shipment item id
        $shipmentItemIds = [];
        foreach ($shipmentItems as $item) {
            $shipmentItemIds[] = $item['order_item_id'];
        }
        foreach ($shipmentItems as $item) {
            /** @var \Magento\Sales\Model\Order\Item $_orderItem */
            $_orderItem = $order->getItemById($item['order_item_id']);
            if ($_orderItem && !$_orderItem->isShipSeparately() && $_orderItem->getChildrenItems() && count($_orderItem->getChildrenItems())) {
                foreach ($_orderItem->getChildrenItems() as $child) {
                    if (array_search($child->getId(), $shipmentItemIds)) {
                        continue;
                    }
                    $data = $child->getData();
                    $data['order_item_id'] = $data['item_id'];
                    $childData[] = $data;
                }
            }
        }
        if (count($childData)) {
            $shipmentItems = array_merge($shipmentItems, $childData);
        }
    }

    /**
     * get qty to ship for children item with bundle and configuration product
     * @param $data
     * @return mixed
     */
    public function getDataToUpdatePackedQty($data)
    {
        $itemIds = array_keys($data);
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderItemCollection */
        $orderItemCollection = $this->orderItemcollectionFactory->create();
        $packedItems = $orderItemCollection->addFieldToFilter('item_id', ['in' => $itemIds]);
        $packedItemsIds = $packedItems->getColumnValues('item_id');
        if (count($packedItemsIds)) {
            //** @var \Magento\Sales\Model\ResourceModel\Sales\Item\Collection  $childrenItems */
            $childrenItems = $this->orderItemcollectionFactory->create()
                ->addFieldToFilter('parent_item_id', ['in' => $packedItemsIds]);
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
     * Get age (h) of Pack Request
     *
     * @param PackRequestInterface $pickRequest
     * @return int
     */
    public function getAge(PackRequestInterface $packRequest)
    {
        if ($packRequest->getAge()) {
            return $packRequest->getAge();
        }
        if ($packRequest->getStatus() == PackRequestInterface::STATUS_PACKED) {
            return $packRequest->getAge();
        }
        $age = $this->dateTime->gmtTimestamp() - $this->dateTime->gmtTimestamp($packRequest->getCreatedAt());
        //$age = intval($age / 3600);
        return $age;
    }

    /**
     * @param $packRequestId
     * @return array
     */
    public function getPackedViewItemsCollection($packRequestId)
    {
        /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequest $packRequest */
        $packRequest = $this->packRequestRepository->get($packRequestId);

        $orderId = $packRequest->getOrderId();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepositoryInterface->get($orderId);

        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $shipmentCollection */
        $shipmentCollection = $this->shipmentCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId);
        $shipmentItems = [];
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            $shipmentItems = array_merge($shipmentItems, $shipment->getAllItems());
        }

        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\PackRequest\PackRequestItem\Collection $packRequestItemCollection */
        $packRequestItemCollection = $this->packRequestItemCollectionFactory->create();
        $packRequestItem = $packRequestItemCollection->addFieldToFilter('pack_request_id', $packRequestId);
        $pRItems = [];
        /** @var \Magestore\FulfilSuccess\Model\PackRequest\PackRequestItem $pItem */
        $pItemIds = $packRequestItem->getColumnValues('item_id');
        foreach ($packRequestItem as $pItem) {
            $pRItems[$pItem->getItemId()] = $pItem->getData();
            if ($pItem->getParentItemId() && !in_array($pItem->getParentItemId(), $pItemIds)) {
                $parentOrderItem = $this->orderItemRepository->get($pItem->getParentItemId());
                if($parentOrderItem->getItemId() && $parentOrderItem->getProductType() != \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                    $pRItems[$pItem->getParentItemId()] = $pItem->getData();
                    $pRItems[$pItem->getParentItemId()]['parent_item_id'] = null;
                }
            }
        }
        $showItemIds = [];
        /** @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem */

        foreach ($shipmentItems as $shipmentItem) {
            if (isset($pRItems[$shipmentItem['order_item_id']])
                && !in_array($pRItems[$shipmentItem['order_item_id']]['item_id'], $showItemIds)
            ) {
                try {
                    $packItem = $pRItems[$shipmentItem['order_item_id']];
                    if (!$packItem['parent_item_id']) {
                        $shipmentItem['qty'] = $packItem['request_qty'] - $packItem['packed_qty'];
                        $shipmentItem['item_id'] = $packItem['item_id'];
                        $shipmentItem->addData($packItem);
                        $items[] = $shipmentItem;
                        $showItemIds[] = $packItem['item_id'];
                    } else {
                        /** @var \Magento\Sales\Model\Order\Item $orderItem */
                        $orderItem = $this->orderItemRepository->get($packItem['parent_item_id']);
                        if ($orderItem->isShipSeparately()) {
                            $shipmentItem['qty'] = $packItem['request_qty'] - $packItem['packed_qty'];
                            $shipmentItem['item_id'] = $packItem['item_id'];
                            $shipmentItem->addData($packItem);
                            $items[] = $shipmentItem;
                            $showItemIds[] = $packItem['item_id'];
                        }
                        if (!$orderItem->isShipSeparately()
                            && !in_array($packItem['parent_item_id'], $showItemIds)
                        ) {
                            $parentItemId = $packItem['parent_item_id'];
                            if (!isset($pRItems[$parentItemId])) {
                                foreach ($shipmentItems as $shipmentParent) {
                                    if ($shipmentParent->getOrderItemId() == $parentItemId) {
                                        $shipmentItem = $shipmentParent;
                                        break;
                                    }
                                }
                                /** @var \Magento\Sales\Model\Order\Item $parentItem */
                                $parentItem = $this->orderItemRepository->get($parentItemId);
                                $shipmentItem['qty'] = $packItem['request_qty'] - $packItem['packed_qty'];
                                $shipmentItem['order_item_id'] = $parentItemId;
                                $shipmentItem->addData($parentItem->getData());
                                $shipmentItem->addData($packItem);
                                $shipmentItem['item_id'] = $parentItemId;
                                $shipmentItem['product_id'] = $parentItem->getProductId();
                                $shipmentItem['parent_item_id'] = null;
                                $shipmentItem['item_name'] = $parentItem->getName();
                                $shipmentItem['item_sku'] = $packItem['item_sku'];
                                $shipmentItem['sku'] = $packItem['item_sku'];
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
     * Move items in PackRequest to Prepare Fulfil
     * Change status of Pack Request to Packed
     *
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     */
    public function moveItemsToNeedToShip(PackRequestInterface $packRequest)
    {
        $isMSIEnable = $this->fulfilManagement->isMSIEnable();
        if (!$isMSIEnable) {
            $warehouseStockRegistry = $this->objectManager->get('Magestore\InventorySuccess\Api\Warehouse\WarehouseStockRegistryInterface');
            $orderItemManagement = $this->objectManager->get('Magestore\InventorySuccess\Api\Warehouse\OrderItemManagementInterface');
        }
        $packItems = $this->packRequestRepository->getItemList($packRequest);
        $moveItems = [];
        if (count($packItems)) {
            /**
             * @var \Magestore\FulfilSuccess\Api\Data\PackRequestItemInterface $packItem
             */
            $requestQtyChange = 0;
            foreach ($packItems as $packItem) {
                if ($remainQty = $packItem->getRequestQty() - $packItem->getPackedQty()) {
                    $moveItems[$packItem->getItemId()] = [
                        PackRequestItemInterface::ITEM_ID => $packItem->getItemId(),
                        'qty' => $remainQty,
                        OrderSuccessOrderItemInterface::QTY_PREPARESHIP => -$remainQty,
                    ];
                    $requestQtyChange -= $remainQty;
                    if (!$isMSIEnable) {
                        // increase available qty on ordered warehouse
                        $this->queryProcessor->start('pack_move_to_need_to_ship');
                        // change qty on ordered warehouse and picked warehouse
                        $pickedWarehouse = $packRequest->getWarehouseId();
                        $orderedWarehouse = $orderItemManagement->getWarehouseByItemId($packItem->getItemId());
                        if ($pickedWarehouse != $orderedWarehouse) {
                            $qtyChanges = [\Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface::QTY_TO_SHIP => -$remainQty];
                            // decrease qty to ship on packed warehouse
                            $increaseQueries = $warehouseStockRegistry->prepareChangeProductQty($pickedWarehouse, $packItem->getProductId(), $qtyChanges);
                            foreach ($increaseQueries as $increaseQuery) {
                                $this->queryProcessor->addQuery($increaseQuery, 'pack_move_to_need_to_ship');
                            }
                            $qtyChanges = [\Magestore\InventorySuccess\Api\Data\Warehouse\ProductInterface::QTY_TO_SHIP => $remainQty];
                            // increase qty to ship on ordered warehouse
                            $decreaseQueries = $warehouseStockRegistry->prepareChangeProductQty($orderedWarehouse, $packItem->getProductId(), $qtyChanges);
                            foreach ($decreaseQueries as $decreaseQuery) {
                                $this->queryProcessor->addQuery($decreaseQuery, 'pack_move_to_need_to_ship');
                            }
                        }

                        $this->queryProcessor->process('pack_move_to_need_to_ship');
                    }
                }
            }
        }
        if (count($moveItems)) {
            /* update Pick Request status*/
            if ($packRequest->getStatus() == PackRequestInterface::STATUS_PACKING) {
                $packRequest->setStatus(PackRequestInterface::STATUS_CANCELED);
            }
            if ($packRequest->getStatus() == PackRequestInterface::STATUS_PARTIAL_PACK) {
                $packRequest->setStatus(PackRequestInterface::STATUS_PACKED);
            }
            $this->packRequestRepository->save($packRequest);

            /* update pack_qty of items in Sales Sales */
            $this->orderFulfilItemRepository->massUpdatePrepareShipQty($moveItems);
        }
        return $this;
    }

    /**
     * get track carrier
     * @param $packRequestId
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection|null
     */
    public function getTrackingCarriers($packRequestId)
    {
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\Collection $packageCollection */
        $packageCollection = $this->packageCollectionFactory->create()
            ->addFieldToFilter('pack_request_id', $packRequestId);
        $trackIds = $packageCollection->getColumnValues('track_id');
        if (count($trackIds)) {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection $trackCollection */
            $trackCollection = $this->trackCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in' => $trackIds]);
            if ($trackCollection->getSize()) {
                return $trackCollection;
            }
        }
        return null;
    }

    /**
     * @param \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest
     * @param string|int $resource
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return void
     */
    public function setSourceCodeForShipment($packRequest, $resource, $shipment)
    {
        /** @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement */
        $fulfilManagement = $this->objectManager->get('Magestore\FulfilSuccess\Api\FulfilManagementInterface');
        if ($fulfilManagement->isMSIEnable()) {
            if (!$resource) {
                $resource = $packRequest->getSourceCode();
            }
            /** @var \Magento\Framework\App\RequestInterface $request */
            $request = $this->objectManager->get('Magento\Framework\App\RequestInterface');
            $request->setParams(['sourceCode' => $resource]);
            /** @var \Magento\Framework\Event\ManagerInterface $eventManager */
            $eventManager = $this->objectManager->get('Magento\Framework\Event\ManagerInterface');
            $eventManager->dispatch(
                'fulfilsuccess_set_source_code_during_create_package',
                ['request' => $request, 'shipment' => $shipment]
            );
            $sourceCode = $request->getParam('sourceCode');
            $shipmentExtension = $shipment->getExtensionAttributes();

            if (empty($shipmentExtension)) {
                $shipmentExtension = $this->objectManager->create('Magento\Sales\Api\Data\ShipmentExtension');
            }
            $shipmentExtension->setSourceCode($sourceCode);
            $shipment->setExtensionAttributes($shipmentExtension);
        }
    }
}