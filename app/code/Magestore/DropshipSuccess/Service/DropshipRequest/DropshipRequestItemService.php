<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service\DropshipRequest;

use Magestore\DropshipSuccess\Api\DropshipRequestItemRepositoryInterface;
use Magestore\DropshipSuccess\Model\DropshipRequest\ItemFactory;
use Magestore\DropshipSuccess\Service\ItemService;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use Magento\InventoryReservationsApi\Model\ReservationBuilderInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class DropshipRequestItemService
 * @package Magestore\DropshipSuccess\Service\DropshipRequest
 */
class DropshipRequestItemService
{
    /**
     * @var DropshipRequestItemRepositoryInterface
     */
    protected $dropshipRequestItemRepository;

    /**
     * @var ItemFactory
     */
    protected $dropshipRequestItemFactory;

    /**
     * @var ItemService
     */
    protected $itemService;
    /**
     * @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\CollectionFactory
     */
    protected $dropshipRequestItemCollectionFactory;
    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;
    /**
     * @var \Magestore\DropshipSuccess\Api\MultiSourceInventory\StockManagementInterface
     */
    private $stockManagement;

    /**
     * @var AppendReservationsInterface
     */
    private $appendReservations;
    /**
     * @var ReservationBuilderInterface
     */
    private $reservationBuilder;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * DropshipRequestItemService constructor.
     * @param DropshipRequestItemRepositoryInterface $dropshipRequestItemRepository
     * @param ItemFactory $dropshipRequestItemFactory
     * @param ItemService $itemService
     * @param \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\CollectionFactory $dropshipRequestItemCollectionFactory
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param \Magestore\DropshipSuccess\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param AppendReservationsInterface $appendReservations
     * @param ReservationBuilderInterface $reservationBuilder
     * @param SerializerInterface $serializer
     */
    public function __construct(
        DropshipRequestItemRepositoryInterface $dropshipRequestItemRepository,
        ItemFactory $dropshipRequestItemFactory,
        ItemService $itemService,
        \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\CollectionFactory $dropshipRequestItemCollectionFactory,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        \Magestore\DropshipSuccess\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        AppendReservationsInterface $appendReservations,
        ReservationBuilderInterface $reservationBuilder,
        SerializerInterface $serializer
    ) {
        $this->dropshipRequestItemRepository = $dropshipRequestItemRepository;
        $this->dropshipRequestItemFactory = $dropshipRequestItemFactory;
        $this->itemService = $itemService;
        $this->dropshipRequestItemCollectionFactory = $dropshipRequestItemCollectionFactory;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->stockManagement = $stockManagement;
        $this->appendReservations = $appendReservations;
        $this->reservationBuilder = $reservationBuilder;
        $this->serializer = $serializer;
    }

    /**
     * @param \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest
     * @param \Magento\Sales\Model\Order\Item $item
     * @param $requestQty
     * @return \Magestore\DropshipSuccess\Model\DropshipRequest\Item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addItemToDropshipRequest($dropshipRequest, $item, $requestQty)
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest\Item $dropshipRequestItem */
        $dropshipRequestItem = $this->dropshipRequestItemFactory->create();
        $dropshipRequestItem->setDropshipRequestId($dropshipRequest->getId());
        $dropshipRequestItem->setParentItemId($item->getParentItemId());
        $dropshipRequestItem->setItemId($item->getId());
        $dropshipRequestItem->setQtyRequested($requestQty);
        $dropshipRequestItem->setItemName($item->getName());
        $dropshipRequestItem->setItemSku($item->getSku());
        $this->dropshipRequestItemRepository->save($dropshipRequestItem);
        /* update qty_prepareship in Order Item */
        $changeQty = $dropshipRequestItem->getQtyRequested();
        $this->itemService->updatePrepareShipQty($item, $changeQty);
        return $dropshipRequestItem;
    }

    /**
     * @param $dropshipRequestId
     * @return \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection
     */
    public function getItemsInDropship($dropshipRequestId)
    {
        /** @var \Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Item\Collection $dropshipRequestItemCollection */
        $dropshipRequestItemCollection = $this->dropshipRequestItemCollectionFactory->create();
        $dropshipRequestItemCollection->addFieldToFilter('dropship_request_id', $dropshipRequestId);
        return $dropshipRequestItemCollection;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param float $requestQty
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function subtractQtyToShip($order, $item, $requestQty){
        $stockId = $this->stockManagement->getStockIdFromOrder($order);
        /* increase available qty in ordered stock by shipped qty*/
        if ($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE && !$item->isShipSeparately()) {
            $this->_subtractQtyToShipInOrderWarehouseForBundleProduct($item, $requestQty, $stockId);
        } else {
            $this->_subtractQtyToShipInOrderWarehouse($item, $requestQty, $stockId);
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param float $cancelQty
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function backQtyToShipOfCancelingItem($order, $item, $cancelQty) {
        $stockId = $this->stockManagement->getStockIdFromOrder($order);
        /* increase qty_to_ship in ordered stock by shipped qty */
        if ($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE && $item->getProduct()->getData('shipment_type') == 0) {
            $this->_increaseQtyToShipInOrderWarehouseForBundleProduct($item, $cancelQty, $stockId);
        } else {
            $this->_increaseQtyToShipInOrderWarehouse($item, $cancelQty, $stockId);
        }
    }

    /**
     * @param string $itemSku
     * @param int $stockId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function isManageStockSku($itemSku, $stockId)
    {
        $stockItemConfiguration = $this->getStockItemConfiguration->execute(
            $itemSku,
            $stockId
        );
        if (!$stockItemConfiguration->isManageStock()) {
            //We don't need to Manage Stock
            return false;
        }
        return true;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param float $requestQty
     * @param int $stockId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function _subtractQtyToShipInOrderWarehouse($item, $requestQty, $stockId) {
        if (!$this->isManageStockSku($item->getSku(), $stockId))
            return $this;
        $orderItem = $this->_getSimpleOrderItem($item);
        $this->_updateQtyProcess($orderItem, $stockId, abs($requestQty), 'dropship_created');
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param float $requestQty
     * @param int $stockId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function _subtractQtyToShipInOrderWarehouseForBundleProduct($item, $requestQty, $stockId) {
        $orderItems = $this->_getSimpleOrderItems($item);
        foreach ($orderItems as $orderItem){
            $this->_subtractQtyToShipInOrderWarehouse($orderItem, $requestQty, $stockId);
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param float $qtyCancel
     * @param int $stockId
     * @return $this
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function _increaseQtyToShipInOrderWarehouse($item, $qtyCancel, $stockId) {
        if (!$this->isManageStockSku($item->getSku(), $stockId))
            return $this;
        $orderItem = $this->_getSimpleOrderItem($item);
        $this->_updateQtyProcess($orderItem, $stockId, -abs($qtyCancel), 'dropship_canceled');
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param float $qtyCancel
     * @param int $stockId
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Validation\ValidationException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function _increaseQtyToShipInOrderWarehouseForBundleProduct($item, $qtyCancel, $stockId) {
        $orderItems = $this->_getSimpleOrderItems($item);
        foreach ($orderItems as $orderItem){
            $this->_increaseQtyToShipInOrderWarehouse($orderItem, $qtyCancel, $stockId);
        }
    }
    /**
     * Get simple item from ship item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return \Magento\Sales\Model\Order\Item
     */
    public function _getSimpleOrderItem($item)
    {
        $simpleItem = $item;
        if ($item->getProduct()->isComposite()) {
            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                foreach ($item->getChildrenItems() as $childItem) {
                    $simpleItem = $childItem;
                    break;
                }
            }
        }
        return $simpleItem;
    }
    /**
     * Get simple item from ship item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return \Magento\Sales\Model\Order\Item[]|array
     */
    public function _getSimpleOrderItems($item)
    {
        if ($item->getProduct()->isComposite()) {
            if ($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                return $item->getChildrenItems();
            }
        }
        return [];
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param int $stockId
     * @param float $requestQty
     * @param string $eventType
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function _updateQtyProcess($orderItem, $stockId, $requestQty, $eventType = 'dropship_created'){
        $reservation = $this->reservationBuilder
            ->setSku($orderItem->getSku())
            ->setQuantity($requestQty)
            ->setStockId($stockId)
            ->setMetadata($this->serializer->serialize(['event_type' => $eventType, 'object_type'=>'order','object_id'=>$orderItem->getOrderId()]))
            ->build();
        $this->appendReservations->execute([$reservation]);
    }
}