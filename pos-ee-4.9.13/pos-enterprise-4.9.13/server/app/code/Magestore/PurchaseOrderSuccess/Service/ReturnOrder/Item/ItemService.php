<?php

namespace Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item;

use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;
use Magestore\SupplierSuccess\Api\Data\SupplierProductInterface;

class ItemService {
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\CollectionFactory
     */
    protected $itemCollectioFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface
     */
    protected $returnOrderItemRepository;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\ItemFactory
     */
    protected $returnItemFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepository;
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory\SourceManagementInterface
     */
    protected $sourceManagement;
    /**
     * @var \Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory
     */
    protected $sourceItemCollectionFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $updateFields = [
        ReturnOrderItemInterface::QTY_RETURNED
    ];

    /**
     * ItemService constructor.
     * @param \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
     * @param \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface $returnOrderItemRepository
     * @param \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\ItemFactory $returnItemFactory
     * @param \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\CollectionFactory $itemCollectioFactory
     * @param \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface $returnOrderRepository
     * @param \Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement
     * @param \Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory $sourceItemCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $connection
     */
    function __construct(
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderItemRepositoryInterface $returnOrderItemRepository,
        \Magestore\PurchaseOrderSuccess\Model\ReturnOrder\ItemFactory $returnItemFactory,
        \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\CollectionFactory $itemCollectioFactory,
        \Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface $returnOrderRepository,
        \Magestore\PurchaseOrderSuccess\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement,
        \Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory $sourceItemCollectionFactory,
        \Magento\Framework\App\ResourceConnection $connection
    ) {
        $this->itemCollectioFactory = $itemCollectioFactory;
        $this->supplierProductService = $supplierProductService;
        $this->returnOrderItemRepository = $returnOrderItemRepository;
        $this->returnItemFactory = $returnItemFactory;
        $this->returnOrderRepository = $returnOrderRepository;
        $this->sourceManagement = $sourceManagement;
        $this->sourceItemCollectionFactory = $sourceItemCollectionFactory;
        $this->connection = $connection;

    }

    /**
     * Get return order product collection from return order id and product ids
     *
     * @param int $returnId
     * @param array $productIds
     * @return \Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\Item\Collection
     */
    public function getProductsByReturnOrderId($returnId, $productIds = []){
        $collection = $this->itemCollectioFactory->create();
        $collection->addFieldToFilter('return_id', $returnId);
        if (!empty($productIds)) {
            $collection->addFieldToFilter('product_id', ['in' => $productIds]);
        }
        return $collection;
    }

    public function getProductIdOnCurrentWarehouse($warehouseId) {
        $sourceItemCollection = $this->sourceItemCollectionFactory->create()
            ->addFieldToFilter('source_code', $warehouseId);

        $sourceItemCollection->getSelect()->joinLeft(
            ['catalog_product' => $this->connection->getTableName('catalog_product_entity')],
            "main_table.sku = catalog_product.sku",
            ['entity_id']
        );
        return $sourceItemCollection->getColumnValues('entity_id');
    }

    public function getTotalQtyProductInSource($sourceCode, $productSku) {
        $sourceItemsMap = $this->sourceManagement->getSourceItemsMap($productSku, [$sourceCode]);
        if (!isset($sourceItemsMap[$sourceCode])) {
            return 0;
        } else {
            return $sourceItemsMap[$sourceCode]->getQuantity();
        }
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
            $prdOnWarehouse = $this->getProductIdOnCurrentWarehouse($params['warehouse_id']);
            $supplierProductIds = $this->supplierProductService
                ->getProductsBySupplierId($params['supplier_id'])
                ->addFieldToFilter('product_id',['in' => $prdOnWarehouse])
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
     * @param array $result
     * @param int $productId
     * @param array|null $productData
     * @return array
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
     * Add product to return order
     *
     * @param string $returnId
     * @param array $productData
     * @return bool
     */
    public function addProductToReturnOrder($returnId, $productsData = []){
        $productIds = array_column($productsData, SupplierProductInterface::PRODUCT_ID);
        $returnProductIds = $this->itemCollectioFactory->create()
            ->addFieldToFilter(ReturnOrderItemInterface::RETURN_ID, $returnId)
            ->addFieldToFilter(ReturnOrderItemInterface::PRODUCT_ID, ['in' => $productIds])
            ->getColumnValues(ReturnOrderItemInterface::PRODUCT_ID);
        $returnOrder = $this->returnOrderRepository->get($returnId);
        $returnProductsData = $this->prepareProductDataToReturnOrder(
            $returnId, $productsData, $returnProductIds, []
        );
        return $this->returnOrderItemRepository->addProductsToReturnOrder($returnProductsData);
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder
     * @param int $supplierId
     * @param array $productData
     * @return $this
     */
    public function updateProductDataToReturnOrder($returnOrder, $productsData = []){
        $returnId = $returnOrder->getReturnOrderId();
        if(empty($productsData))
            return $this;
        $returnProductData = $this->getProductsByReturnOrderId($returnId, array_keys($productsData))
            ->getData();
        $productsData = $this->prepareProductDataToReturnOrder($returnId, $returnProductData, [], $productsData);
        foreach ($productsData as $productId => $productData){
            $returnItem = $this->returnItemFactory->create()->addData($productData)
                ->setId($productData[ReturnOrderItemInterface::RETURN_ITEM_ID]);
            $this->returnOrderItemRepository->save($returnItem);
        }
        return $this;
    }

    /**
     * Prepare data add product to return order
     *
     * @param int $returnId
     * @param array $productsData
     * @param array $returnProductIds
     * @return array
     */
    public function prepareProductDataToReturnOrder(
        $returnId, $productsData = [], $returnProductIds = [], $updateData = []
    ){
        $returnProductsData = [];
        foreach ($productsData as $productData){
            if(in_array($productData[SupplierProductInterface::PRODUCT_ID], $returnProductIds))
                continue;
            $productId = $productData[SupplierProductInterface::PRODUCT_ID];
            $returnProductsData[$productId] = [
                ReturnOrderItemInterface::RETURN_ID => $returnId,
                ReturnOrderItemInterface::PRODUCT_ID => $productData[SupplierProductInterface::PRODUCT_ID],
                ReturnOrderItemInterface::PRODUCT_SKU => $productData[SupplierProductInterface::PRODUCT_SKU],
                ReturnOrderItemInterface::PRODUCT_NAME => $productData[SupplierProductInterface::PRODUCT_NAME],
                ReturnOrderItemInterface::PRODUCT_SUPPLIER_SKU => $productData[SupplierProductInterface::PRODUCT_SUPPLIER_SKU],
            ];
            if(isset($updateData[$productId])){
                $returnProductsData[$productId] = array_merge(
                    $returnProductsData[$productId],
                    $updateData[$productId],
                    [ReturnOrderItemInterface::RETURN_ITEM_ID => $productData[ReturnOrderItemInterface::RETURN_ITEM_ID]]
                );
            }
        }
        return $returnProductsData;
    }
}