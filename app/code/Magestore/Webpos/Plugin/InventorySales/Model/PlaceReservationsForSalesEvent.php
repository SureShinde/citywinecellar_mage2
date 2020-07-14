<?php

namespace Magestore\Webpos\Plugin\InventorySales\Model;

use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use Magento\InventoryReservationsApi\Model\ReservationBuilderInterface;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventorySales\Model\SalesEventToArrayConverter;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PlaceReservationsForSalesEvent
 *
 * @package Magestore\Webpos\Plugin\InventorySales\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PlaceReservationsForSalesEvent
{
    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ReservationBuilderInterface
     */
    private $reservationBuilder;

    /**
     * @var AppendReservationsInterface
     */
    private $appendReservations;

    /**
     * @var GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var SalesEventToArrayConverter
     */
    private $salesEventToArrayConverter;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * PlaceReservationsForSalesEvent constructor.
     *
     * @param \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param ReservationBuilderInterface $reservationBuilder
     * @param AppendReservationsInterface $appendReservations
     * @param GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param SerializerInterface $serializer
     * @param SalesEventToArrayConverter $salesEventToArrayConverter
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Framework\Registry $registry
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        ReservationBuilderInterface $reservationBuilder,
        AppendReservationsInterface $appendReservations,
        GetProductTypesBySkusInterface $getProductTypesBySkus,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        SerializerInterface $serializer,
        SalesEventToArrayConverter $salesEventToArrayConverter,
        ResourceConnection $resourceConnection,
        \Magento\Framework\Registry $registry
    ) {
        $this->stockManagement = $stockManagement;
        $this->orderRepository = $orderRepository;
        $this->reservationBuilder = $reservationBuilder;
        $this->appendReservations = $appendReservations;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->serializer = $serializer;
        $this->salesEventToArrayConverter = $salesEventToArrayConverter;
        $this->resourceConnection = $resourceConnection;
        $this->registry = $registry;
    }

    /**
     * Around execute reservation
     *
     * @param \Magento\InventorySales\Model\PlaceReservationsForSalesEvent $subject
     * @param callable $proceed
     * @param array $items
     * @param SalesChannelInterface $salesChannel
     * @param SalesEventInterface $salesEvent
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function aroundExecute(
        \Magento\InventorySales\Model\PlaceReservationsForSalesEvent $subject,
        callable $proceed,
        array $items,
        SalesChannelInterface $salesChannel,
        SalesEventInterface $salesEvent
    ) {
        if (empty($items)) {
            return $proceed($items, $salesChannel, $salesEvent);
        }
        $salesEventType = $salesEvent->getType();
        $salesEventObjectType = $salesEvent->getObjectType();
        if ((
                $salesEventType == SalesEventInterface::EVENT_ORDER_CANCELED ||
                $salesEventType == SalesEventInterface::EVENT_SHIPMENT_CREATED ||
                $salesEventType == SalesEventInterface::EVENT_CREDITMEMO_CREATED
            ) &&
            $salesEventObjectType == SalesEventInterface::OBJECT_TYPE_ORDER
        ) {
            $orderId = $salesEvent->getObjectId();
            $order = $this->orderRepository->get($orderId);
            $stockId = $this->stockManagement->getStockIdFromOrder($order);
            if (!$stockId) {
                if ($salesEventType != SalesEventInterface::EVENT_ORDER_CANCELED &&
                    $salesEventType != SalesEventInterface::EVENT_CREDITMEMO_CREATED
                ) {
                    $this->registry->unregister('pos_create_shipment_place_reservation');
                    if (!$order->getPosLocationId()) {
                        $this->registry->register('pos_create_shipment_place_reservation', true);
                    }
                    return $proceed($items, $salesChannel, $salesEvent);
                } else {
                    $stockId = $this->stockManagement->getStockIdBySalesChannel($salesChannel);
                }
            }
            $skus = [];
            /** @var ItemToSellInterface $item */
            foreach ($items as $item) {
                $skus[] = $item->getSku();
            }
            $productTypes = $this->getProductTypesBySkus->execute($skus);
            $reservations = [];
            /** @var ItemToSellInterface $item */
            foreach ($items as $item) {
                $currentSku = $item->getSku();
                $skuNotExistInCatalog = !isset($productTypes[$currentSku]);
                if ($skuNotExistInCatalog ||
                    $this->isSourceItemManagementAllowedForProductType->execute($productTypes[$currentSku])
                ) {
                    $reservations[] = $this->reservationBuilder
                        ->setSku($item->getSku())
                        ->setQuantity((float)$item->getQuantity())
                        ->setStockId($stockId)
                        ->setMetadata(
                            $this->serializer->serialize($this->salesEventToArrayConverter->execute($salesEvent))
                        )
                        ->build();
                }
            }
            return $this->appendReservations->execute($reservations);
        }
        return $proceed($items, $salesChannel, $salesEvent);
    }
}
