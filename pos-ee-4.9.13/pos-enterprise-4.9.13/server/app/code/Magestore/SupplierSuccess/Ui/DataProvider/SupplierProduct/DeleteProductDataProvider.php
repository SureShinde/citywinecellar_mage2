<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider\SupplierProduct;

use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class ProductDataProvider
 */
class DeleteProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var mixed  */
    protected $collection;

    /**
     * @var RequestInterface
     */
    protected $requestInterface;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\ProductService
     */
    protected $supplierProductService;

    /**
     * SupplierDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $requestInterface,
        \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->requestInterface = $requestInterface;
        $this->collectionFactory = $collectionFactory;
        $this->supplierProductService = $supplierProductService;
        $this->collection = $this->getModifyCollection();
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

    public function getModifyCollection()
    {
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $supplierId = $this->requestInterface->getParam('supplier_id', null);
        return $collection->addFieldToFilter('supplier_id', $supplierId);
    }
}
