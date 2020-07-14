<?php

namespace Magestore\Webpos\Plugin\InventoryShippingAdminUi\Ui\DataProvider;

use Magento\Sales\Model\Order\Item;

class SourceSelectionDataProvider
{

    /**
     * @var \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface
     */
    protected $stockManagement;


    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;
    /**
     * @var \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;
    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    private $locationRepository;
    /**
     * @var \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface
     */
    private $getSkuFromOrderItem;

    /**
     * @var \Magento\InventoryShippingAdminUi\Ui\DataProvider\GetSourcesByStockIdSkuAndQty
     */
    private $getSourcesByStockIdSkuAndQty;

    /**
     * @var \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * SourceSelectionDataProvider constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
     * @param \Magento\InventoryShippingAdminUi\Ui\DataProvider\GetSourcesByStockIdSkuAndQty $getSourcesByStockIdSkuAndQty
     * @param \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration
     */
    public function __construct(
        \Magestore\Webpos\Api\MultiSourceInventory\StockManagementInterface $stockManagement,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem,
        \Magento\InventoryShippingAdminUi\Ui\DataProvider\GetSourcesByStockIdSkuAndQty $getSourcesByStockIdSkuAndQty,
        \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration
    )
    {
        $this->stockManagement = $stockManagement;
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->locationRepository = $locationRepository;
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
        $this->getSourcesByStockIdSkuAndQty = $getSourcesByStockIdSkuAndQty;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
    }

    public function aroundGetData(\Magento\InventoryShippingAdminUi\Ui\DataProvider\SourceSelectionDataProvider $subject,
                                  callable $proceed)
    {
        $data = [];
        $orderId = $this->request->getParam('order_id');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
        $websiteId = $order->getStore()->getWebsiteId();

        $stockId = $this->stockManagement->getStockIdFromOrder($order);
        if (!$stockId) {
            return $proceed();
        }

        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getIsVirtual()
                || $orderItem->getLockedDoShip()
                || $orderItem->getHasChildren()) {
                continue;
            }

            $item = $orderItem->isDummy(true) ? $orderItem->getParentItem() : $orderItem;
            $qty = $item->getSimpleQtyToShip();
            $qty = $this->castQty($item, $qty);
            $sku = $this->getSkuFromOrderItem->execute($item);
            $data[$orderId]['items'][] = [
                'orderItemId' => $item->getId(),
                'sku' => $sku,
                'product' => $this->getProductName($orderItem),
                'qtyToShip' => $qty,
                'sources' => $this->getSources($stockId, $sku, $qty),
                'isManageStock' => $this->isManageStock($sku, $stockId)
            ];
        }
        $data[$orderId]['websiteId'] = $websiteId;
        $data[$orderId]['order_id'] = $orderId;
        foreach ($this->sources as $code => $name) {
            $data[$orderId]['sourceCodes'][] = [
                'value' => $code,
                'label' => $name
            ];
        }

        return $data;
    }

    /**
     * @param int $stockId
     * @param string $sku
     * @param float $qty
     * @return array
     * @throws NoSuchEntityException
     */
    private function getSources(int $stockId, string $sku, float $qty)
    {
        $sources = $this->getSourcesByStockIdSkuAndQty->execute($stockId, $sku, $qty);
        foreach ($sources as $source) {
            $this->sources[$source['sourceCode']] = $source['sourceName'];
        }
        return $sources;
    }

    /**
     * @param $itemSku
     * @param $stockId
     * @return bool
     * @throws LocalizedException
     */
    private function isManageStock($itemSku, $stockId)
    {
        $stockItemConfiguration = $this->getStockItemConfiguration->execute($itemSku, $stockId);

        return $stockItemConfiguration->isManageStock();
    }

    /**
     * Generate display product name
     * @param Item $item
     * @return null|string
     */
    private function getProductName(Item $item)
    {
        //TODO: need to transfer this to html block and render on Ui
        $name = $item->getName();
        if ($parentItem = $item->getParentItem()) {
            $name = $parentItem->getName();
            $options = [];
            if ($productOptions = $parentItem->getProductOptions()) {
                if (isset($productOptions['options'])) {
                    $options = array_merge($options, $productOptions['options']);
                }
                if (isset($productOptions['additional_options'])) {
                    $options = array_merge($options, $productOptions['additional_options']);
                }
                if (isset($productOptions['attributes_info'])) {
                    $options = array_merge($options, $productOptions['attributes_info']);
                }
                if (count($options)) {
                    foreach ($options as $option) {
                        $name .= '<dd>' . $option['label'] . ': ' . $option['value'] . '</dd>';
                    }
                } else {
                    $name .= '<dd>' . $item->getName() . '</dd>';
                }
            }
        }

        return $name;
    }

    /**
     * @param Item $item
     * @param string|int|float $qty
     * @return float|int
     */
    private function castQty(Item $item, $qty)
    {
        if ($item->getIsQtyDecimal()) {
            $qty = (double)$qty;
        } else {
            $qty = (int)$qty;
        }

        return $qty > 0 ? $qty : 0;
    }

}