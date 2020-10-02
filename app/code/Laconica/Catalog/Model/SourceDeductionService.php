<?php

namespace Laconica\Catalog\Model;

use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventorySalesApi\Api\GetStockBySalesChannelInterface;
use Magento\InventorySourceDeductionApi\Model\GetSourceItemBySourceCodeAndSku;
use Magento\InventorySourceDeductionApi\Model\SourceDeductionRequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Laconica\Catalog\Helper\Config;

class SourceDeductionService implements \Magento\InventorySourceDeductionApi\Model\SourceDeductionServiceInterface
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
     * @var Config
     */
    private $configHelper; // CUSTOM CODE

    public function __construct(
        Config $configHelper, // CUSTOM CODE
        SourceItemsSaveInterface $sourceItemsSave,
        GetSourceItemBySourceCodeAndSku $getSourceItemBySourceCodeAndSku,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        GetStockBySalesChannelInterface $getStockBySalesChannel
    ) {
        $this->sourceItemsSave = $sourceItemsSave;
        $this->getSourceItemBySourceCodeAndSku = $getSourceItemBySourceCodeAndSku;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getStockBySalesChannel = $getStockBySalesChannel;
        $this->configHelper = $configHelper; // CUSTOM CODE
    }

    /**
     * @inheritdoc
     */
    public function execute(SourceDeductionRequestInterface $sourceDeductionRequest): void
    {
        $sourceItems = [];
        $sourceCode = $sourceDeductionRequest->getSourceCode();
        $salesChannel = $sourceDeductionRequest->getSalesChannel();

        $stockId = $this->getStockBySalesChannel->execute($salesChannel)->getStockId();
        foreach ($sourceDeductionRequest->getItems() as $item) {
            $itemSku = $item->getSku();
            $qty = $item->getQty();
            $stockItemConfiguration = $this->getStockItemConfiguration->execute(
                $itemSku,
                $stockId
            );

            // CUSTOM CODE
            if (!$stockItemConfiguration->isManageStock() || $this->configHelper->backendManageStock()) {
                //We don't need to Manage Stock
                continue;
            }
            // END CUSTOM CODE
            $sourceItem = $this->getSourceItemBySourceCodeAndSku->execute($sourceCode, $itemSku);
            if (($sourceItem->getQuantity() - $qty) >= 0) {
                $sourceItem->setQuantity($sourceItem->getQuantity() - $qty);
                $sourceItems[] = $sourceItem;
            } else {
                throw new LocalizedException(
                    __('Not all of your products are available in the requested quantity.')
                );
            }
        }

        if (!empty($sourceItems)) {
            $this->sourceItemsSave->execute($sourceItems);
        }
    }
}