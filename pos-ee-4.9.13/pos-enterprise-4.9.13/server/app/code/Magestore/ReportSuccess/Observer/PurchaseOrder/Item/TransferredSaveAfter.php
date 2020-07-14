<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Observer\PurchaseOrder\Item;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use \Magento\CatalogInventory\Model\Stock\Item;

/**
 * Class TransferredSaveAfter
 * @package Magestore\ReportSuccess\Observer\PurchaseOrder\Item
 */
class TransferredSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Item
     */
    protected $stockItem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * TransferredSaveAfter constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Item $stockItem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Stock\Item $stockItem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
    )
    {
        $this->objectManager = $objectManager;
        $this->_productFactory = $productFactory;
        $this->stockItem = $stockItem;
        $this->scopeConfig = $scopeConfig;
        $this->reportManagement = $reportManagement;
    }

    /**
     * @param EventObserver $observer
     * @return EventObserver
     */
    public function execute(EventObserver $observer)
    {
        if (get_class($observer->getData('object')) == 'Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\Transferred') {
            /** @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\Transferred $transferredItem */
            $transferredItem = $observer->getData('object');
            /** @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item $purchaseOrderItem */
            $purchaseOrderItem = $transferredItem->getPurchaseOrderItem();
            /** @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder $purchaseOrder */
            $purchaseOrder = $purchaseOrderItem->getPurchaseOrder();
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->_productFactory->create()->loadByAttribute('sku', $purchaseOrderItem->getProductSku());
            if ($product->getId()) {
                if ($purchaseOrder->getSubtotal() && $this->scopeConfig->getValue('reportsuccess/include_costing_method/include_shipping')) {
                    $shippingFeePerItem = $purchaseOrderItem->getCost() / $purchaseOrder->getSubtotal() * $purchaseOrder->getShippingCost();
                } else {
                    $shippingFeePerItem = 0;
                }
                $mac = $product->getMac();
                $cost = $product->getCost();
                if (!$mac && $cost) {
                    $mac = $cost;
                }

                $tax = $discount = 0;
                if ($this->scopeConfig->getValue('reportsuccess/include_costing_method/include_discount')) {
                    $discount = $purchaseOrderItem->getDiscount();
                }
                if ($this->scopeConfig->getValue('reportsuccess/include_costing_method/include_tax')) {
                    $tax = $purchaseOrderItem->getTax();
                }

                $landedCost = $purchaseOrderItem->getCost() * (100 + $tax - $discount) / 100 + $shippingFeePerItem;
                if ($purchaseOrder->getCurrencyRate()) {
                    $landedCost /= $purchaseOrder->getCurrencyRate();
                }
                if (!$mac) {
                    $mac = $landedCost;
                } else {
                    $stockQty = 0;
                    if ($this->reportManagement->isMSIEnable()) {
                        /** @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySku */
                        $getSourceItemsBySku = $this->objectManager
                            ->get('Magento\InventoryApi\Api\GetSourceItemsBySkuInterface');
                        $sourceItems = $getSourceItemsBySku->execute($product->getSku());
                        foreach ($sourceItems as $sourceItem) {
                            $stockQty += $sourceItem->getQuantity();
                        }
                    } else {
                        $stockItem = $this->stockItem->load($product->getId(), 'product_id');
                        $stockQty = $stockItem->getQty();
                    }
                    $currentStockValue = $stockQty * $mac;
                    $newStockValue = $landedCost * $transferredItem->getQtyTransferred();
                    $newQty = $stockQty + $transferredItem->getQtyTransferred();
                    $mac = ($currentStockValue + $newStockValue) / $newQty;
                }
                $mac = round($mac, 2);
                $product->setData('mac', $mac)->save();
            }
        }
//        if ( get_class($observer->getData('object')) == 'Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Item\Refunded'){
//
//        }
        return $observer;
    }
}