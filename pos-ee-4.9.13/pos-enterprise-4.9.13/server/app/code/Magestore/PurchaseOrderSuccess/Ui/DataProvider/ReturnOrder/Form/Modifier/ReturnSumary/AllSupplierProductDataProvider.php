<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\Form\Modifier\ReturnSumary;

use Magestore\SupplierSuccess\Api\Data\SupplierProductInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;

class AllSupplierProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService
     */
    protected $returnItemService;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Service\ReturnOrder\Item\ItemService $returnItemService,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->supplierProductService = $supplierProductService;
        $this->returnItemService = $returnItemService;
        $this->collection = $this->getAllSupplierProductCollection();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();
        return $items;
    }

    /**
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $collection
     */
    public function getAllSupplierProductCollection()
    {
        $supplierId = $this->request->getParam('supplier_id', null);
        $returnId = $this->request->getParam('return_id', null);
        $warehouseId = $this->request->getParam('warehouse_id', null);
        $collection = $this->supplierProductService->getProductsBySupplierId($supplierId);
        if ($returnId) {
            $productIds = $this->returnItemService->getProductsByReturnOrderId($returnId)
                ->getColumnValues(ReturnOrderItemInterface::PRODUCT_ID);
//            if (!empty($productIds)) {
//                $collection->addFieldToFilter(SupplierProductInterface::PRODUCT_ID, ['nin' => $productIds]);
//            }

            $listSupplierProductIds = $this->supplierProductService->getProductsBySupplierId($supplierId)->getColumnValues(SupplierProductInterface::PRODUCT_ID);
            $listWarehouseProductIds = $this->returnItemService->getProductIdOnCurrentWarehouse($warehouseId);
            // get only product on warehouse return
            $listCorrectProductIds = array_intersect($listSupplierProductIds, $listWarehouseProductIds);
            $collection->addFieldToFilter(SupplierProductInterface::PRODUCT_ID, ['in' => $listCorrectProductIds]);

            if(count($productIds)) {
                $collection->addFieldToFilter(SupplierProductInterface::PRODUCT_ID, ['nin' => $productIds]);
            }
        }
        return $collection;
    }

    /**
     * Set Query limit
     *
     * @param int $offset
     * @param int $size
     * @return void
     */
    public function setLimit($offset, $size)
    {
        $this->getCollection()->setPageSize($size);
        $this->getCollection()->setCurPage($offset);
    }
}