<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\SupplierSuccess\Api\Data\SupplierProductInterface;

/**
 * Class ItemService
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Product
 */
class ItemService
{
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory
     */
    protected $itemCollectioFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface
     */
    protected $purchaseOrderItemRepository;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping
     */
    protected $taxShippingService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\ItemFactory
     */
    protected $purchaseItemFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var array
     */
    protected $updateFields = [
        PurchaseOrderItemInterface::COST,
        PurchaseOrderItemInterface::TAX,
        PurchaseOrderItemInterface::DISCOUNT,
        PurchaseOrderItemInterface::QTY_ORDERRED
    ];

    /**
     * ProductService constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory $itemCollectioFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\CollectionFactory $itemCollectioFactory,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderItemRepositoryInterface $purchaseOrderItemRepository,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\ItemFactory $purchaseItemFactory,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
    ){
        $this->itemCollectioFactory = $itemCollectioFactory;
        $this->purchaseOrderItemRepository = $purchaseOrderItemRepository;
        $this->purchaseItemFactory = $purchaseItemFactory;
        $this->supplierProductService = $supplierProductService;
        $this->taxShippingService = $taxShippingService;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->priceCurrency = $priceCurrency;
        $this->_scopeConfig = $_scopeConfig;
    }

    /**
     * Get purchase order product collection from purchase order id and product ids
     *
     * @param int $purchaseId
     * @param array $productIds
     * @return \Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\Item\Collection
     */
    public function getProductsByPurchaseOrderId($purchaseId, $productIds = []){
        $collection = $this->itemCollectioFactory->create();
        $collection->addFieldToFilter('purchase_order_id', $purchaseId);
        if (!empty($productIds)) {
            $collection->addFieldToFilter('product_id', ['in' => $productIds]);
        }
        return $collection;
    }

    /**
     * @param array $params
     * @return array
     */
    public function processIdsProductModal($params = []){
        if(isset($params['selected'])){
            return $params['selected'];
        }
        if(isset($params['excluded'])){
            $supplierProductIds = $this->supplierProductService
                ->getProductsBySupplierId($params['supplier_id'])
                ->getColumnValues('product_id');
            if($params['excluded'] == 'false'){
                return $supplierProductIds;
            }
            if(is_array($params['excluded'])){
                return array_diff($supplierProductIds,$params['excluded']);
            }
        }
        return [];
    }

    /**
     * Process param to save product data
     *
     * @param array $params
     * @return array
     */
    public function processUpdateProductParams($params = []){
        $result = [];
        if(isset($params['selected_products']) && is_string($params['selected_products'])){
            $selectedProduct = json_decode($params['selected_products'], true);
            foreach ($selectedProduct as $productId => $productData){
                $result = $this->processProductData($result, $productId, $productData);
            }
        }
        return $result;
    }

    /**
     * Process product data
     *
     * @param int $productId
     * @param array|null $productData
     */
    public function processProductData($result, $productId, $productData){
        if(is_string($productData))
            $productData = json_decode($productData, true);
        foreach ($this->updateFields as $field){
            if(!isset($productData[$field])) {
                continue;
            }
            if($productData[$field] != $productData[$field . '_old']){
                $result[$productId] = $productData;
                return $result;
            }
        }
        return $result;
    }

    /**
     * Add product to purchase order
     *
     * @param string $purchaseId
     * @param array $productData
     * @return bool
     */
    public function addProductToPurchaseOrder($purchaseId, $productsData = [], $params = []){
        $productIds = array_column($productsData, SupplierProductInterface::PRODUCT_ID);
        $purchaseProductIds = $this->itemCollectioFactory->create()
            ->addFieldToFilter(PurchaseOrderItemInterface::PURCHASE_ORDER_ID, $purchaseId)
            ->addFieldToFilter(PurchaseOrderItemInterface::PRODUCT_ID, ['in' => $productIds])
            ->getColumnValues(PurchaseOrderItemInterface::PRODUCT_ID);
        $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
        $purchaseProductsData = $this->prepareProductDataToPurchaseOrder(
            $purchaseId, $productsData, $purchaseProductIds, [], $purchaseOrder->getCurrencyRate()
        );
        if(isset($params['suggest_qty']) && count($params['suggest_qty'])) {
            $suggestQtyParams = $this->modifySuggestParam($params['suggest_qty']);
            $purchaseProductsData = $this->addDefaultQty($purchaseProductsData, $suggestQtyParams);

            // add pricing list
            $priceList = $this->getListPricingList($purchaseOrder);
            if(count($priceList)) {
                $purchaseProductsData = $this->updatePriceList($priceList, $purchaseProductsData);
            }
        }
        return $this->purchaseOrderItemRepository->addProductsToPurchaseOrder($purchaseProductsData);
    }

    public function updatePriceList($priceList, $purchaseProductsData) {
        foreach ($purchaseProductsData as $productId => &$data) {
            if(isset($priceList[$productId]) &&
                $data[PurchaseOrderItemInterface::QTY_ORDERRED] > $priceList[$productId]['minimal_qty']) {
                $data[PurchaseOrderItemInterface::COST] = $priceList[$productId]['cost'];
            }
        }
        return $purchaseProductsData;
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $puchaseOrder
     * @return array
     */
    public function getListPricingList($puchaseOrder) {
        if (!$this->_scopeConfig->getValue('suppliersuccess/pricelist/enable')) {
            $priceList = [];
            return $priceList;
        }

        $time = $puchaseOrder->getPurchasedAt();
        $supplierId = $puchaseOrder->getSupplierId();
        /** @var \Magestore\SupplierSuccess\Service\Supplier\PricingListService $supplierService */
        $supplierService =  \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magestore\SupplierSuccess\Service\Supplier\PricingListService'
        );
        $priceList = $supplierService->getProductCost(null, $supplierId, $time);
        $priceListJson = [];
        foreach ($priceList as $price){
            $price['cost'] = $this->priceCurrency->convert($price['cost'] * $puchaseOrder->getCurrencyRate());
            $priceListJson[$price['product_id']] = $price;
        }
        return $priceListJson;
    }

    public function addDefaultQty($purchaseProductsData, $suggestQty) {
        foreach ($purchaseProductsData as $productId => &$data) {
            if(isset($suggestQty[$productId])) {
                $data[PurchaseOrderItemInterface::QTY_ORDERRED] = $suggestQty[$productId];
            }
        }
        return $purchaseProductsData;
    }

    public function modifySuggestParam($suggestQtyParams) {
        $data = [];
        foreach ($suggestQtyParams as $param) {
            $data[$param['id']] = $param['qty'];
        }
        return $data;
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @param int $supplierId
     * @param array $productData
     * @return $this
     */
    public function updateProductDataToPurchaseOrder($purchaseOrder, $productsData = []){
        $purchaseId = $purchaseOrder->getPurchaseOrderId();
        if(empty($productsData))
            return $this;
        $purchaseProductData = $this->getProductsByPurchaseOrderId($purchaseId, array_keys($productsData))
            ->getData();
        $productsData = $this->prepareProductDataToPurchaseOrder($purchaseId, $purchaseProductData, [], $productsData);
        $productsData = $this->updateDefaultProductData($purchaseOrder->getSupplierId(), $productsData);
        foreach ($productsData as $productId => $productData){
            $purchaseItem = $this->purchaseItemFactory->create()->addData($productData)
                ->setId($productData[PurchaseOrderItemInterface::PURCHASE_ORDER_ITEM_ID]);
            $this->purchaseOrderItemRepository->save($purchaseItem);
        }
        return $this;
    }

    /**
     * Prepare data add product to purchase order
     *
     * @param int $purchaseId
     * @param array $productsData
     * @param array $purchaseProductIds
     * @return array
     */
    public function prepareProductDataToPurchaseOrder(
        $purchaseId, $productsData = [], $purchaseProductIds = [], $updateData = [], $rate = 1
    ){
        $purchaseProductsData = [];
//        $defaultTax = $this->taxShippingService->getDefaultTax();
        foreach ($productsData as $productData){
            if(in_array($productData[SupplierProductInterface::PRODUCT_ID], $purchaseProductIds))
                continue;
            $productId = $productData[SupplierProductInterface::PRODUCT_ID];
            $cost = (float)$productData[SupplierProductInterface::COST] * $rate;
            $purchaseProductsData[$productId] = [
                PurchaseOrderItemInterface::PURCHASE_ORDER_ID => $purchaseId,
                PurchaseOrderItemInterface::PRODUCT_ID => $productData[SupplierProductInterface::PRODUCT_ID],
                PurchaseOrderItemInterface::PRODUCT_SKU => $productData[SupplierProductInterface::PRODUCT_SKU],
                PurchaseOrderItemInterface::PRODUCT_NAME => $productData[SupplierProductInterface::PRODUCT_NAME],
                PurchaseOrderItemInterface::PRODUCT_SUPPLIER_SKU => $productData[SupplierProductInterface::PRODUCT_SUPPLIER_SKU],
                PurchaseOrderItemInterface::ORIGINAL_COST => $cost,
                PurchaseOrderItemInterface::COST => $cost,
                PurchaseOrderItemInterface::TAX => $productData[SupplierProductInterface::TAX],
//                PurchaseOrderItemInterface::TAX => $defaultTax?$defaultTax:$productData[SupplierProductInterface::TAX],
            ];
            if(isset($updateData[$productId])){
                $purchaseProductsData[$productId] = array_merge(
                    $purchaseProductsData[$productId],
                    $updateData[$productId],
                    [PurchaseOrderItemInterface::PURCHASE_ORDER_ITEM_ID => $productData[PurchaseOrderItemInterface::PURCHASE_ORDER_ITEM_ID]]
                );
            }
        }
        return $purchaseProductsData;
    }

    /**
     * Set default value for product data
     *
     * @param int $supplierId
     * @param array $productData
     * @return array
     */
    public function updateDefaultProductData($supplierId, $productData = []){
//        $defaultTax = $this->taxShippingService->getDefaultTax();
        foreach ($productData as $key => $data){
//            if($data[PurchaseOrderItemInterface::TAX] == ''){
//                $data[PurchaseOrderItemInterface::TAX] = $defaultTax;
//            }
            if($data[PurchaseOrderItemInterface::COST] == ''){
                $cost = $this->supplierProductService->getProductsBySupplierId($supplierId, [$data['product_id']])
                    ->getFirstItem()->getCost();
                $data[PurchaseOrderItemInterface::COST] = $cost;
            }
            $productData[$key] = $data;
        }
        return $productData;
    }
}
