<?php

namespace Magestore\Webpos\Plugin\InventoryShippingAdminUi\Block\Adminhtml\Shipment;

use Magento\Framework\Registry;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class Inventory
{
    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;
    /**
     * @var \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    protected $getSourcesAssignedToStock;

    /**
     * ShipmentInventory constructor.
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStock
     */
    public function __construct(
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStock
    )
    {
        $this->stockManagement = $stockManagement;
        $this->locationRepository = $locationRepository;
        $this->getSourcesAssignedToStock = $getSourcesAssignedToStock;
    }

    /**
     * @param \Magento\InventoryShippingAdminUi\Block\Adminhtml\Shipment\Inventory $subject
     * @param callable $proceed
     * @return callable
     */
    public function aroundGetSourceCode(\Magento\InventoryShippingAdminUi\Block\Adminhtml\Shipment\Inventory $subject,
                                        callable $proceed)
    {
        $shipment = $subject->getShipment();
        if ($shipment->getOrder()) {
            $stockId = $this->stockManagement->getStockIdFromOrder($shipment->getOrder());
            if ($stockId) {
                $sourceCode = $this->getSourceCode($stockId);
                if ($sourceCode) {
                    return $sourceCode;
                }
            }
        }
        return $proceed();
    }


    /**
     * @param $stockId
     * @return bool
     */
    public function getSourceCode($stockId)
    {
        $sources = $this->getSourcesAssignedToStock->execute($stockId);
        if ($sources) {
            foreach ($sources as $source) {
                if ($source->getEnabled()) {
                    return $source->getSourceCode();
                }
            }
        }
        return false;
    }
}