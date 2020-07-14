<?php

namespace Magestore\Storepickup\Plugin\InventoryShippingAdminUi\Ui\DataProvider;

use Magento\Sales\Model\Order\Item;

class SourceSelectionDataProvider
{
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
     * @var \Magestore\Storepickup\Helper\Data
     */
    private $storepickupHelperData;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $_storepickupFactory;

    /**
     * @var array
     */
    private $sources = [];

    /**
     * SourceSelectionDataProvider constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem
     * @param \Magento\InventoryShippingAdminUi\Ui\DataProvider\GetSourcesByStockIdSkuAndQty $getSourcesByStockIdSkuAndQty
     * @param \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param \Magestore\Storepickup\Helper\Data $storepickupHelperData
     * @param \Magestore\Storepickup\Model\StoreFactory $storepickupFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        \Magento\InventorySalesApi\Model\GetSkuFromOrderItemInterface $getSkuFromOrderItem,
        \Magento\InventoryShippingAdminUi\Ui\DataProvider\GetSourcesByStockIdSkuAndQty $getSourcesByStockIdSkuAndQty,
        \Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface $getStockItemConfiguration,
        \Magestore\Storepickup\Helper\Data $storepickupHelperData,
        \Magestore\Storepickup\Model\StoreFactory $storepickupFactory
    )
    {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->getSkuFromOrderItem = $getSkuFromOrderItem;
        $this->getSourcesByStockIdSkuAndQty = $getSourcesByStockIdSkuAndQty;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->storepickupHelperData = $storepickupHelperData;
        $this->_storepickupFactory = $storepickupFactory;
    }

    public function aroundGetData(\Magento\InventoryShippingAdminUi\Ui\DataProvider\SourceSelectionDataProvider $subject,
                                  callable $proceed)
    {
        $data = [];
        $orderId = $this->request->getParam('order_id');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
        if ($order->getShippingMethod() !== 'storepickup_storepickup' || !$this->storepickupHelperData->isMSISourceEnable()) {
            return $proceed();
        } else {
            if (!$order->getStorepickupId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Cannot find the Storepickup ID!')
                );
            } else {
                $storepickupId = $order->getStorepickupId();
            }
        }

        $websiteId = $order->getStore()->getWebsiteId();
        $stockId = (int)$this->stockByWebsiteIdResolver->execute((int)$websiteId)->getStockId();
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getIsVirtual()
                || $orderItem->getLockedDoShip()
                || $orderItem->getHasChildren()
            ) {
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
                'sources' => $this->getSources($stockId, $sku, $qty, $storepickupId),
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
    private function getSources(int $stockId, string $sku, float $qty, int $storepickupId)
    {
        $sources = $this->getSourcesByStockIdSkuAndQty->execute($stockId, $sku, $qty);
        $result = [];
        $storepickup = $this->_storepickupFactory->create()->load($storepickupId);
        foreach ($sources as $source) {
            if ($source['sourceCode'] == $storepickup->getSourceCode()) {
                $this->sources[$source['sourceCode']] = $source['sourceName'];
                $result[] = $source;
            }
        }
        return $result;
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