<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider\Product\Form\Modifier\Supplier;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory;


class SupplierList extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection
     */
    protected $collection;

    /**
     * SupplierList constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->getSupplierCollection();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getSupplierCollection()
    {
        $productId = $this->request->getParam('product_id');
        $condition = 'main_table.supplier_id = supplier_product.supplier_id';
        if ($productId)
            $condition .= " AND supplier_product.product_id = $productId";
        $this->collection = $this->collectionFactory->create();
        $this->collection->getSelect()->joinLeft(
            ['supplier_product' => $this->collection->getTable('os_supplier_product')],
            $condition,
            ['product_supplier_sku', 'cost', 'tax']
        )->group('main_table.supplier_id');;
        return $this->collection;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->getCollection()->addFieldToFilter(
            $filter->getField(),
            [$filter->getConditionType() => $filter->getValue()]
        );
    }
}