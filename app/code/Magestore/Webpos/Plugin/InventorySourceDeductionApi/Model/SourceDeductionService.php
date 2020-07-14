<?php

namespace Magestore\Webpos\Plugin\InventorySourceDeductionApi\Model;

use Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventorySalesApi\Api\GetStockBySalesChannelInterface;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;

class SourceDeductionService
{

    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSave;
    /**
     * @var GetSourceItemBySourceCodeAndSku
     */
    private $getSourceItemBySourceCodeAndSku;
    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;
    /**
     * @var GetStockBySalesChannelInterface
     */
    private $getStockBySalesChannel;

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * SourceDeductionService constructor.
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param GetStockBySalesChannelInterface $getStockBySalesChannel
     */
    public function __construct(
        SourceItemsSaveInterface $sourceItemsSave,
        GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        GetStockBySalesChannelInterface $getStockBySalesChannel,
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Sales\Model\OrderRepository $orderRepository
    )
    {
        $this->sourceItemsSave = $sourceItemsSave;
        $this->getSourceItemBySourceCodeAndSku = $getSourceItemBySourceCodeAndSku;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getStockBySalesChannel = $getStockBySalesChannel;
        $this->stockManagement = $stockManagement;
        $this->orderRepository = $orderRepository;

    }

    public function aroundExecute(\Magento\InventorySourceDeductionApi\Model\SourceDeductionService $subject,
                                  callable $proceed,
                                  SourceDeductionRequestInterface $sourceDeductionRequest)
    {
        $sourceItems = [];
        $sourceCode = $sourceDeductionRequest->getSourceCode();
        $salesEvent = $sourceDeductionRequest->getSalesEvent();
        $salesEventType = $salesEvent->getType();
        $salesEventObjectType = $salesEvent->getObjectType();

        if ((
                $salesEventType == SalesEventInterface::EVENT_ORDER_CANCELED ||
                $salesEventType == SalesEventInterface::EVENT_SHIPMENT_CREATED ||
                $salesEventType == SalesEventInterface::EVENT_CREDITMEMO_CREATED
            ) && $salesEventObjectType == SalesEventInterface::OBJECT_TYPE_ORDER
        ) {
            /* integrated with POS */
            $orderId = $salesEvent->getObjectId();
            $order = $this->orderRepository->get($orderId);
            $stockId = $this->stockManagement->getStockIdFromOrder($order);
            $locationStockId = $this->stockManagement->getStockId();
            if (!$stockId) {
                if ($salesEventType == SalesEventInterface::EVENT_CREDITMEMO_CREATED) {
                    if ($locationStockId) {
                        $stockId = $locationStockId;
                    }
                }
                if (!$stockId) {
                    return $proceed($sourceDeductionRequest);
                }
            }
            foreach ($sourceDeductionRequest->getItems() as $item) {
                $itemSku = $item->getSku();
                $qty = $item->getQty();
                try {
                    if (!$this->isManageStockSku($itemSku, $stockId)) {
                        continue;
                    }
                } catch (\Exception $e) {
                    if ($salesEventType != SalesEventInterface::EVENT_CREDITMEMO_CREATED) {
                        throw $e;
                    }
                    if (!$locationStockId) {
                        throw $e;
                    }
                    $sources = $this->stockManagement->getLinkedSourceCodesByStockId($locationStockId);
                    if (empty($sources)) {
                        throw $e;
                    }
                    $sourceCode = $sources[0];
                    $this->stockManagement->createSourceItem($itemSku, $sourceCode);
                }
                if ($salesEventType == SalesEventInterface::EVENT_CREDITMEMO_CREATED) {
                    if ($locationStockId) {
                        $sources = $this->stockManagement->getLinkedSourceCodesByStockId($locationStockId);
                        if (!empty($sources)) {
                            $sourceCode = $sources[0];
                        }
                    }
                }
                $sourceItem = $this->getSourceItemBySourceCodeAndSku->execute($sourceCode, $itemSku);
                if ($locationStockId && $salesEventType == SalesEventInterface::EVENT_SHIPMENT_CREATED) {
                    $sourceItem->setQuantity($sourceItem->getQuantity() - $qty);
                    $sourceItems[] = $sourceItem;
                } else {
                    if (($sourceItem->getQuantity() - $qty) >= 0) {
                        $sourceItem->setQuantity($sourceItem->getQuantity() - $qty);
                        $sourceItems[] = $sourceItem;
                    } else {
                        throw new LocalizedException(
                            __('Not all of your products are available in the requested quantity.')
                        );
                    }
                }
            }
            if (!empty($sourceItems)) {
                $this->sourceItemsSave->execute($sourceItems);
            }
            return true;
        }

        return $proceed($sourceDeductionRequest);
    }

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
}