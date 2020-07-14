<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Receive;

/**
 * Class ProductToReceive
 * @package Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Receive
 */
class ProductToReceive extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\Collection
     */
    protected $collection;

    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $type;

    protected $_receiveProductCollectionFactory;


    /**
     * ProductToReceive constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory $collectionFactory,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory $receiveProductCollectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product\Type $type,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;

        $this->_collectionFactory = $collectionFactory;
        $this->_receiveProductCollectionFactory = $receiveProductCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->type = $type;
        $this->collection = $this->getProductCollection();

    }

    /**
     * @return \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\Collection
     */
    public function getProductCollection()
    {
        $inventoryTransferId = $this->request->getParam('inventorytransfer_id');
        /** @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\Collection $collection */
        $collection = $this->_collectionFactory->create();

        if ($inventoryTransferId) {
            /** @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\Collection $receiveProductCollection */
            $receiveProductCollection = $this->_receiveProductCollectionFactory->create();

            $listSkuReceived = $receiveProductCollection->getListProductSkuReceive($inventoryTransferId);
            if (count($listSkuReceived)) {
                $collection->getSelect()->where('((qty_transferred - qty_received > 0) OR (qty_transferred = 0 AND main_table.product_sku NOT IN (?)))', $listSkuReceived);
            }

        }
        $collection->getTransferedProducts($inventoryTransferId);
        return $collection;
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return mixed|void
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (
            $filter->getField() == 'product_sku'
            || $filter->getField() == 'product_id'
        ) {
            $filter->setField('main_table.'. $filter->getField());
        } elseif ($filter->getField() == 'available_qty_to_receive') {
            $filter->setField(new \Zend_Db_Expr('qty_transferred - qty_received'));
        }
        parent::addFilter($filter);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($this->request->getParam('inventorytransfer_id')) {
            /** @var \Magento\Catalog\Helper\Image $imageCalalogHelper */
            $imageCalalogHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Image::class);
            $productTypes = $this->type->getOptionArray();
            foreach ($data['items'] as &$item) {
                $item['available_qty_to_receive'] = (float) $item['available_qty_to_receive'];
                if(strpos($item['image_url'], 'no_selection') !== false) {
                    $item['image_url'] = $imageCalalogHelper->getDefaultPlaceholderUrl('thumbnail');
                }
                if (isset($item['type_id'])) {
                    $item['type'] = $productTypes[$item['type_id']];
                }
            }
        }
        return $data;
    }

}
