<?php

namespace Magestore\DropshipSuccess\Plugin\InventorySales\Model;

use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\Framework\Registry;
use Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface;

class PlaceReservationsForSalesEvent
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * PlaceReservationsForSalesEvent constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    )
    {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\InventorySales\Model\PlaceReservationsForSalesEvent $subject
     * @param callable $proceed
     * @param array $items
     * @param SalesChannelInterface $salesChannel
     * @param SalesEventInterface $salesEvent
     * @return bool
     */
    public function aroundExecute(\Magento\InventorySales\Model\PlaceReservationsForSalesEvent $subject,
                                  callable $proceed,
                                  array $items,
                                  SalesChannelInterface $salesChannel,
                                  SalesEventInterface $salesEvent)
    {
        $salesEventType = $salesEvent->getType();
        $salesEventObjectType = $salesEvent->getObjectType();
        if ($salesEventType == SalesEventInterface::EVENT_SHIPMENT_CREATED
            && $salesEventObjectType == SalesEventInterface::OBJECT_TYPE_ORDER
            && $this->registry->registry(DropshipShipmentInterface::CREATE_SHIPMENT_BY_DROPSHIP)
        ) {
            /* Do nothing when create shipment from dropship request */
            return;
        }

        return $proceed($items, $salesChannel, $salesEvent);
    }
}