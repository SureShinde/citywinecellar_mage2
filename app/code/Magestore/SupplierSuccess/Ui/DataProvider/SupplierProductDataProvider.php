<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider;

//use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Grid\CollectionFactory;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory;
use Magestore\SupplierSuccess\Model\Locator\LocatorInterface;

/**
 * Class ProductDataProvider
 */
class SupplierProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var mixed  */
    protected $collection;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * SupplierProductDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param LocatorInterface $locator
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        LocatorInterface $locator,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->locator = $locator;
        $supplierId = '';
        if ($this->locator->getSession(\Magestore\SupplierSuccess\Api\Data\SupplierInterface::SUPPLIER_ID)) {
            $supplierId = $this->locator->getSession(\Magestore\SupplierSuccess\Api\Data\SupplierInterface::SUPPLIER_ID);
        }
        $this->collection = $collectionFactory->create()->addFieldToFilter('supplier_id', $supplierId);
    }
}
