<?php

namespace Magestore\DropshipSuccess\Plugin\InventorySourceDeductionApi\Model;

use Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\Framework\Registry;
use Magestore\DropshipSuccess\Api\Data\DropshipShipmentInterface;

class SourceDeductionService
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * SourceDeductionService constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    )
    {
        $this->registry = $registry;

    }

    public function aroundExecute(\Magento\InventorySourceDeductionApi\Model\SourceDeductionService $subject,
                                  callable $proceed,
                                  SourceDeductionRequestInterface $sourceDeductionRequest)
    {
        $salesEvent = $sourceDeductionRequest->getSalesEvent();
        $salesEventType = $salesEvent->getType();
        $salesEventObjectType = $salesEvent->getObjectType();

        if ($salesEventType == SalesEventInterface::EVENT_SHIPMENT_CREATED
            && $salesEventObjectType == SalesEventInterface::OBJECT_TYPE_ORDER
            && $this->registry->registry(DropshipShipmentInterface::CREATE_SHIPMENT_BY_DROPSHIP)
        ) {
            /* Do nothing if create shipment from dropship request */
            return;
        }

        return $proceed($sourceDeductionRequest);
    }
}