<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier\PurchaseSumary;

use Magestore\SupplierSuccess\Api\Data\SupplierProductInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;

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
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService
     */
    protected $purchaseItemService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig
     */
    protected $productConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    public $filterMap = [
        'product_id' => 'entity_id',
        'product_sku' => 'sku',
        'product_name' => 'name'
    ];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $purchaseItemService,
        \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->supplierProductService = $supplierProductService;
        $this->purchaseItemService = $purchaseItemService;
        $this->productConfig = $productConfig;
        $this->productCollectionFactory = $productCollectionFactory;
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
        if($this->checkProductSource()) {
            $data = $this->getCollection()->toArray();
        } else {
            $collection = $this->getCollection();
            $items = $collection->toArray();
            foreach ($items as &$item) {
                // initialize primary field name
                $item['supplier_product_id'] = $item['product_id'];
                $item['product_name'] = $item['name'];
            }
            $data = [
                'totalRecords' => $collection->getSize(),
                'items' => array_values($items)
            ];
        }
        return $data;
    }

    /**
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $collection
     */
    public function getAllSupplierProductCollection()
    {
        $supplierId = $this->request->getParam('supplier_id', null);
        $purchaseId = $this->request->getParam('purchase_id', null);
        if($this->checkProductSource()) {
            $collection = $this->supplierProductService->getProductsBySupplierId($supplierId);
        } else {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect(['name']);
            $collection->getSelect()
                ->columns(['product_id' => 'entity_id'])
                ->columns(['product_sku' => 'sku']);
        }
        if ($purchaseId) {
            $productIds = $this->purchaseItemService->getProductsByPurchaseOrderId($purchaseId)
                ->getColumnValues(PurchaseOrderItemInterface::PRODUCT_ID);
            if (!empty($productIds)) {
                if($this->checkProductSource()) {
                    $collection->addFieldToFilter(SupplierProductInterface::PRODUCT_ID, ['nin' => $productIds]);
                } else {
                    $collection->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
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

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if(isset($this->filterMap[$filter->getField()]) && !$this->checkProductSource()) {
            $filter->setField($this->filterMap[$filter->getField()]);
        }
        parent::addFilter($filter);
    }

    public function addOrder($field, $direction)
    {
        if(isset($this->filterMap[$field]) && !$this->checkProductSource()) {
            $this->getCollection()->addOrder($this->filterMap[$field], $direction);
        } else {
            $this->getCollection()->addOrder($field, $direction);
        }
    }

    public function checkProductSource() {
        return (boolean)($this->productConfig->getProductSource() == \Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource::TYPE_SUPPLIER);
    }
}