<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier\PurchaseSumary;

use Magestore\PurchaseOrderSuccess\Model\ResourceModel\BackOrderProduct\CollectionFactory as CollectionFactory;
/**
 * Class BackOrderProductDataProvider
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\Form\Modifier\PurchaseSumary
 */
class BackOrderProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $itemCollectionFactory;

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
     * @var array
     */
    public $filterMap = [
        'product_id' => 'entity_id',
        'product_sku' => 'sku',
        'product_name' => 'name'
    ];

    /**
     * BackOrderProductDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory
     * @param \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $purchaseItemService
     * @param \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        CollectionFactory $productCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\ItemService $purchaseItemService,
        \Magestore\PurchaseOrderSuccess\Service\Config\ProductConfig $productConfig,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->moduleManager = $moduleManager;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->supplierProductService = $supplierProductService;
        $this->purchaseItemService = $purchaseItemService;
        $this->productConfig = $productConfig;
        $this->collection = $this->getBackOrderProductCollection();
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

        $collection = $this->getCollection();
        $items = $collection->toArray();
        if(!$this->checkProductSource()) {
            foreach ($items as &$item) {
                $item['product_id'] = $item['entity_id'];
                $item['product_sku'] = $item['sku'];
                $item['product_name'] = $item['name'];
                $item['supplier_product_id'] = $item['entity_id'];
            }
        }
        return [
            'totalRecords' => $collection->getSize(),
            'items' => array_values($items)
        ];
    }

    /**
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $collection
     */
    public function getBackOrderProductCollection()
    {
        $supplierId = $this->request->getParam('supplier_id', null);
        $purchaseId = $this->request->getParam('purchase_id', null);
        if (!$purchaseId) {
            $collection = $this->productCollectionFactory->create()
                ->setPageSize(0)->setCurPage(1);
        } else {
            $collection = $this->productCollectionFactory->create()
                ->addFilterSupplierAndPurchase($supplierId, $purchaseId);
        }
        return $collection;
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return mixed|void
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->filterMap[$filter->getField()]) && !$this->checkProductSource()) {
            $filter->setField($this->filterMap[$filter->getField()]);
        }
        parent::addFilter($filter);
    }

    /**
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
        if (isset($this->filterMap[$field]) && !$this->checkProductSource()) {
            $this->getCollection()->addOrder($this->filterMap[$field], $direction);
        } else {
            $this->getCollection()->addOrder($field, $direction);
        }
    }

    /**
     * @return bool
     */
    public function checkProductSource()
    {
        return (boolean)($this->productConfig->getProductSource() ==
            \Magestore\PurchaseOrderSuccess\Model\System\Config\ProductSource::TYPE_SUPPLIER);
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