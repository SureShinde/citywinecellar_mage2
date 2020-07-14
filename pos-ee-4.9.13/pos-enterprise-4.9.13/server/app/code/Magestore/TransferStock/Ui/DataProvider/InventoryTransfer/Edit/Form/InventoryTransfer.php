<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Edit\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\TransferStock\Model\InventoryTransfer\Option\Status;
use Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\CollectionFactory;

/**
 * Class InventoryTransfer
 * @package Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Form
 */
class InventoryTransfer extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    protected $pool;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magestore\TransferStock\Api\TransferManagementInterface
     */
    protected $transferManagement;
    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory
     */
    protected $transferProductCollectionFactory;
    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var array
     */
    protected $loadData = [];
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $type;

    /**
     * InventoryTransfer constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\TransferStock\Api\TransferManagementInterface $transferManagement
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory $transferProductCollectionFactory
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        \Magento\Framework\Registry $registry,
        \Magestore\TransferStock\Api\TransferManagementInterface $transferManagement,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory $transferProductCollectionFactory,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product\Type $type,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
        $this->_coreRegistry = $registry;
        $this->transferManagement = $transferManagement;
        $this->transferProductCollectionFactory = $transferProductCollectionFactory;
        $this->sourceRepository = $sourceRepository;
        $this->_objectManager = $objectManager;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
         if($this->loadData) {
             return $this->loadData;
         }

         /** @var \Magestore\TransferStock\Model\InventoryTransfer $inventoryTransfer */
        $inventoryTransfer = $this->_coreRegistry->registry('current_inventory_transfer');
        if($inventoryTransfer && $inventoryTransfer->getId()){
            /** @var \Magento\Catalog\Helper\Image $imageCalalogHelper */
            $imageCalalogHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Image::class);
            $inventoryTransferData = $inventoryTransfer->getData();

            // get source name
            $inventoryTransferData['source_warehouse_name'] = $this->sourceRepository->get($inventoryTransfer->getSourceWarehouseCode())->getName();
            $inventoryTransferData['des_warehouse_name'] = $this->sourceRepository->get($inventoryTransfer->getDesWarehouseCode())->getName();
            $inventoryTransferData['status_label'] = ($inventoryTransferData['status'] == Status::STATUS_OPEN) ? __('Open') : __('Closed');

            $this->loadData[$inventoryTransfer->getId()]['transfer_summary']['general_information'] = $inventoryTransferData;
            $products = $this->getProductsListData($inventoryTransfer->getId(), $inventoryTransfer->getSourceWarehouseCode());
            $productTypes = $this->type->getOptionArray();
            if(count($products)) {
                foreach ($products as $product) {
                    if(strpos($product['image_url'], 'no_selection') !== false) {
                        $product['image_url'] = $imageCalalogHelper->getDefaultPlaceholderUrl('thumbnail');
                    }
                    $dataProduct = [
                        'id' => $product['product_id'],
                        'sku' => $product['product_sku'],
                        'name' => $product['product_name'],
                        'qty' => (float)$product['quantity'],
                        'qty_transferred' => (float)$product['qty_transferred'],
                        'qty_received' => (float)$product['qty_received'],
                        'image' => $product['image_url'],
                        'barcode' => $product['barcode'],
                        'type' => isset($productTypes[$product['type_id']])
                            ? $productTypes[$product['type_id']]
                            : $product['type_id']
                    ];
                    $this->loadData[$inventoryTransfer->getId()]['transfer_summary']['product_list']['product_dynamic_grid']['links']['product_list'][] = $dataProduct;
                }
            }
        }

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->loadData = $modifier->modifyData($this->loadData);
        }
        return $this->loadData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * @param int $transferId
     * @param string $sendingSourceCode
     * @return array
     */
    public function getProductsListData($transferId, $sendingSourceCode) {
        /** @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\Collection $collection */
        $collection = $this->transferProductCollectionFactory->create();
        $collection->getTransferedProducts($transferId, $sendingSourceCode);
        return $collection->getData();
    }
}