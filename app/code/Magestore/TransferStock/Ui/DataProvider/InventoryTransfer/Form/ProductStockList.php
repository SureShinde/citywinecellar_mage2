<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Form;

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;


class ProductStockList extends ProductDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;
    /**
     * @var \Magestore\TransferStock\Model\InventoryTransferFactory
     */
    protected $inventoryTransferFactory;
    /**
     * @var array
     */
    protected $addedField;
    /**
     * @var
     */
    protected $type;

    /**
     * ProductStockList constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magestore\TransferStock\Model\InventoryTransferFactory $inventoryTransferFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magestore\TransferStock\Model\InventoryTransferFactory $inventoryTransferFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\Product\Type $type,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->request = $request;
        $this->inventoryTransferFactory = $inventoryTransferFactory;
        $this->collection = $this->getProductCollection();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->type = $type;
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
        $productTypes = $this->type->getOptionArray();
        foreach ($items as &$item) {
            if (isset($item['type_id'])) {
                $item['type'] = $productTypes[$item['type_id']];
            }
        }

        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\GlobalStock\Collection');

        return $collection;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->addedField[] = $filter->getField();
        if ($filter->getField() == "source_warehouse_code" && $filter->getValue()) {
            $this->getCollection()->addSourceCodeToFilter($filter->getValue());
        } else if ($filter->getField() == "barcode" && $filter->getValue()) {
            $this->getCollection()->addBarcodeToFilter($filter->getValue());
        } else {
            parent::addFilter($filter);
        }
    }

}
