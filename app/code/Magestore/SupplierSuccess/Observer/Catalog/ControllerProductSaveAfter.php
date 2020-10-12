<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Observer\Catalog;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ControllerProductSaveAfter implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory
     */
    protected $supplierProductCollectionFactory;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;


    /**
     * ControllerProductSaveAfter constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory $supplierProductCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory $supplierProductCollectionFactory,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService
    )
    {
        $this->registry = $registry;
        $this->request = $request;
        $this->supplierProductCollectionFactory = $supplierProductCollectionFactory;
        $this->supplierProductService = $supplierProductService;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $suppliers = $this->request->getParam('suppliers');
        $isAddSupplier = $this->request->getParam('add_supplier') === '1';
        if (!$isAddSupplier)
            return $this;
        if ($suppliers && isset($suppliers['data'])) {
            if (!empty($suppliers['data'])) {
                $data = $this->processParams($product, $suppliers['data']);
                $this->deleteSupplierProduct($product->getId(), array_keys($data));
                $unsaveData = $this->modifySupplierProduct($product->getId(), $data);
                // echo "<pre>";
                // var_dump($unsaveData);
                // die();
                if (!empty($unsaveData)) {
                    $this->addSupplierProduct($unsaveData);
                }
            } else {
                $this->deleteSupplierProduct($product->getId(), [0]);
            }
        } else {
            $this->deleteSupplierProduct($product->getId(), [0]);
        }
        return $this;
    }

    /**
     * Process supplier data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     * @return array
     */
    public function processParams($product, $data = [])
    {
        $result = [];
        foreach ($data as $item) {
            $item['product_id'] = $product->getId();
            $item['product_sku'] = $product->getSku();
            $item['product_name'] = $product->getName();
            $item['supplier_id'] = $item['id'];
            unset($item['id']);
            unset($item['supplier_code']);
            unset($item['position']);
            unset($item['record_id']);
            if(isset($item['supplier_name'])) {
                unset($item['supplier_name']);
            }
            $result[$item['supplier_id']] = $item;
        }
        return $result;
    }

    /**
     * Delete product not in request
     *
     * @param array $supplierId
     */
    public function deleteSupplierProduct($productId, $supplierIds = [])
    {
        $supplierProducts = $this->supplierProductCollectionFactory->create()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('supplier_id', ['nin' => $supplierIds]);
        /** @var \Magestore\SupplierSuccess\Model\Supplier\Product $supplierProduct */
        foreach ($supplierProducts as $supplierProduct) {
            $supplierProduct->delete();
        }
    }

    /**
     * Delete product not in request
     *
     * @param int $productId
     * @param array $supplierProductData
     */
    public function modifySupplierProduct($productId, $supplierProductData = [])
    {
        $supplierProducts = $this->supplierProductCollectionFactory->create()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('supplier_id', ['in' => array_keys($supplierProductData)]);
        /** @var \Magestore\SupplierSuccess\Model\Supplier\Product $supplierProduct */
        foreach ($supplierProducts as $supplierProduct) {
            if (isset($supplierProductData[$supplierProduct->getSupplierId()])) {
                $data = $supplierProductData[$supplierProduct->getSupplierId()];
                unset($supplierProductData[$supplierProduct->getSupplierId()]);
                $supplierProduct->setProductSupplierSku($data['product_supplier_sku']);
                $supplierProduct->setCost($data['cost']);
                $supplierProduct->setTax($data['tax']);
                $supplierProduct->save();
            }
        }
        return $supplierProductData;
    }

    /**
     * Delete product not in request
     *
     * @param array $supplierId
     */
    public function addSupplierProduct($supplierProductData = [])
    {
        $resource = $this->supplierProductCollectionFactory->create()
            ->getResource();
        $table = $resource->getTable('os_supplier_product');
        return $resource->getConnection()->insertMultiple($table, $supplierProductData);
    }
}