<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider;

use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Grid\CollectionFactory;

/**
 * Class ProductDataProvider
 */
class SupplierDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var mixed  */
    protected $collection;


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
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
