<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Observer\Catalog;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection;

/**
 * Class CatalogProductSaveBefore
 *
 * @package Magestore\SupplierSuccess\Observer\Catalog
 */
class CatalogProductSaveBefore implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $supplierProductCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * CatalogProductSaveBefore constructor.
     * @param CollectionFactory $supplierProductCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        CollectionFactory $supplierProductCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->supplierProductCollectionFactory = $supplierProductCollectionFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * Update supplier product data
     *
     * @param EventObserver $observer
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        $isNewProduct = $product->isObjectNew();

        if ($isNewProduct) {
            return;
        }

        /** @var \Magento\Catalog\Model\Product $currentProduct */
        $currentProduct = $this->productFactory->create()->load($product->getId());
        if (!$currentProduct->getId()) {
            return;
        }

        $changedData = [];
        if($product->getSku() != $currentProduct->getSku()) {
            $changedData['product_sku'] = $product->getSku();
        }
        if($product->getName() != $currentProduct->getName()) {
            $changedData['product_name'] = $product->getName();
        }

        if(count($changedData)) {
            /** @var Collection $supplierProductCollection */
            $supplierProductCollection = $this->supplierProductCollectionFactory->create();
            $supplierProductCollection->addFieldToFilter('product_id', ['eq' => $product->getId()]);
            $supplierProductCollection->load();

            /** @var \Magestore\SupplierSuccess\Model\Supplier\Product $supplierProductModel */
            foreach ($supplierProductCollection as $supplierProductModel) {
                $supplierProductModel->addData($changedData)->save();
            }
        }
    }
}