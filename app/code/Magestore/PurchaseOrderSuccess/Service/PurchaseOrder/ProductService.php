<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder;

/**
 * Class ProductService
 *
 * @package Magestore\PurchaseOrderSuccess\Service\PurchaseOrder
 */
class ProductService
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * ProductService constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productResource = $productResource;
    }

    /**
     * Get product data if source product is from All Stores
     *
     * @param array $productIds
     * @param int $supplierId
     * @return array
     */
    public function prepareProductForPO($productIds, $supplierId)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name']);
        $collection->addFieldToSelect('entity_id');
        $collection->addFieldToSelect('sku');
        $collection->addFieldToFilter('entity_id', ['in' => $productIds]);

        $collection->getSelect()->joinLeft(
            ['supplier_product' => $this->productResource->getTable('os_supplier_product')],
            new \Zend_Db_Expr(
                'supplier_product.product_sku = e.sku ' .
                'AND supplier_product.supplier_id = ' . $supplierId
            ),
            ['product_supplier_sku', 'cost', 'tax']
        );

        $data = $collection->toArray();
        foreach ($data as &$item) {
            $item['product_id'] = $item['entity_id'];
            $item['product_sku'] = $item['sku'];
            $item['product_name'] = $item['name'];
            $item['product_supplier_sku'] = $item['product_supplier_sku'] ? $item['product_supplier_sku'] : '';
            $item['cost'] = $item['cost']? $item['cost'] : '';
            $item['tax'] = $item['tax'] ? $item['tax'] : '';
        }
        return $data;
    }
}
