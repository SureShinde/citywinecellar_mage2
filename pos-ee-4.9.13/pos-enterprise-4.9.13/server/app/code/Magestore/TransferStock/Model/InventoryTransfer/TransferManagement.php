<?php

namespace Magestore\TransferStock\Model\InventoryTransfer;

use Magento\InventoryImportExport\Model\Import\SourceItemConvert;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryImportExport\Model\Import\Sources;
use Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterface;
use Magestore\TransferStock\Model\InventoryTransfer\Option\Stage;
use Magestore\TransferStock\Model\InventoryTransfer\Option\Status;

class TransferManagement implements \Magestore\TransferStock\Api\TransferManagementInterface  {
    /**
     * @var \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterfaceFactory
     */
    protected $inventoryTransferFactory;
    /**
     * @var SourceItemConvert
     */
    protected $sourceItemConvert;
    /**
     * @var SourceItemsSaveInterface
     */
    protected $sourceItemsSave;
    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory
     */
    protected $inventoryTransferProductCollectionFactory;
    /**
     * @var \Magestore\TransferStock\Api\MultiSourceInventory\SourceManagementInterface
     */
    protected $sourceManagement;
    /**
     * @var \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterfaceFactory
     */
    protected $inventoryTransferProductFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory
     */
    protected $receiveInterfaceFactory;
    /**
     * @var \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveProductInterfaceFactory
     */
    protected $receiveProductInterfaceFactory;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $type;
    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive\CollectionFactory
     */
    protected $inventoryReceiveProductCollectionFactory;

    /**
     * TransferManagement constructor.
     * @param \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterfaceFactory $inventoryTransferFactory
     * @param SourceItemConvert $sourceItemConvert
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory $inventoryTransferProductCollectionFactory
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory $inventoryReceiveProductCollectionFactory
     * @param \Magestore\TransferStock\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement
     * @param \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterfaceFactory $inventoryTransferProductFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory $receiveInterfaceFactory
     * @param \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveProductInterfaceFactory $receiveProductInterfaceFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferInterfaceFactory $inventoryTransferFactory,
        SourceItemConvert $sourceItemConvert,
        SourceItemsSaveInterface $sourceItemsSave,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory $inventoryTransferProductCollectionFactory,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory $inventoryReceiveProductCollectionFactory,
        \Magestore\TransferStock\Api\MultiSourceInventory\SourceManagementInterface $sourceManagement,
        \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterfaceFactory $inventoryTransferProductFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterfaceFactory $receiveInterfaceFactory,
        \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveProductInterfaceFactory $receiveProductInterfaceFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Catalog\Model\Product\Type $type
    )
    {
        $this->inventoryTransferFactory = $inventoryTransferFactory;
        $this->sourceItemConvert = $sourceItemConvert;
        $this->sourceItemsSave = $sourceItemsSave;
        $this->inventoryTransferProductCollectionFactory = $inventoryTransferProductCollectionFactory;
        $this->sourceManagement = $sourceManagement;
        $this->inventoryTransferProductFactory = $inventoryTransferProductFactory;
        $this->_objectManager = $objectManager;
        $this->receiveInterfaceFactory = $receiveInterfaceFactory;
        $this->receiveProductInterfaceFactory = $receiveProductInterfaceFactory;
        $this->authSession = $authSession;
        $this->type = $type;
        $this->inventoryReceiveProductCollectionFactory = $inventoryReceiveProductCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function startToSendStock($transferId){
        $result = [
            'status' => false
        ];
        /** @var InventoryTransferInterface $transferInventory */
        $transferInventory = $this->inventoryTransferFactory->create()->load($transferId);
        if(!$transferInventory->getInventorytransferId()) {
            $result['message'] = __('Could not find transfer inventory request with ID %1', $transferId);
            return $result;
        } elseif ($transferInventory->getStatus() == \Magestore\TransferStock\Model\InventoryTransfer\Option\Status::STATUS_CLOSED) {
            $result['message'] = __('The transfer inventory request has already closed.');
            return $result;
        }

        $items = $this->getProductFromInventoryTransfer($transferId);
        if(!count($items)) {
            $result['message'] = __('Please select item(s) before sending.');
            return $result;
        }

        $this->processSendProduct($transferInventory, $items);

        $transferInventory->setStage(\Magestore\TransferStock\Model\InventoryTransfer\Option\Stage::STAGE_SENT)->save();

        $result = [
            'status' => true,
            'message' => __('The stock has been sent successfully.')
        ];
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getProductFromInventoryTransfer($transferId) {
        /** @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\Collection $collection */
        $collection = $this->inventoryTransferProductCollectionFactory->create();
        $collection->addTransferIdToFilter($transferId);
        return $collection;
    }


    /**
     * @param InventoryTransferInterface $transferInventory
     * @param \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterface[] $items
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function processSendProduct($transferInventory, $items) {
        $sentItems = [];
        foreach ($items as $item) {
            $sourceItems = $this->sourceManagement->getSourceItemsMap($item->getProductSku(), [$transferInventory->getSourceWarehouseCode()]);
            $sentItems[$item->getProductSku()] = $sourceItems[$transferInventory->getSourceWarehouseCode()]->getQuantity() - $item->getQtyTransferred();
        }

        $this->processChangeQuantity($transferInventory->getSourceWarehouseCode(), $sentItems);
    }

    /**
     * @param string $sourceCode
     * @param array $items
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function processChangeQuantity($sourceCode, $items) {
        $sourceItems = [];
        $packItems = 0;
        $i = 0;

        foreach ($items as $sku => $qty) {
            $sourceItem = [
                Sources::COL_SOURCE_CODE => $sourceCode,
                Sources::COL_SKU => (string)$sku,
                Sources::COL_QTY => $qty,
                Sources::COL_STATUS => ($qty > 0) ? 1 : 0
            ];
            $sourceItems[$packItems][] = $sourceItem;
            $i++;
            if($i == 500) {
                $i = 0;
                $packItems++;
            }
        }

        if (!empty($sourceItems)) {
            foreach ($sourceItems as $items) {
                $items = $this->sourceItemConvert->convert($items);
                $this->sourceItemsSave->execute($items);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteProductsInInventoryTransfer($transferId){
        $products = $this->getProductFromInventoryTransfer($transferId);
        foreach ($products as $product) {
            $product->delete();
        }

        /** @var InventoryTransferInterface $transferInventory */
        $transferInventory = $this->inventoryTransferFactory->create()->load($transferId);
        $transferInventory->setQtyTransferred(0)->save();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function setProductsForInventoryTransfer($transferId, $items) {
        $this->deleteProductsInInventoryTransfer($transferId);

        /** @var InventoryTransferInterface $transferInventory */
        $transferInventory = $this->inventoryTransferFactory->create()->load($transferId);
        $totalQtyToTransfer = 0;

        foreach ($items as $item) {
            /** @var \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterface $transferProduct */
            $transferProduct = $this->inventoryTransferProductFactory->create();
            $data = [
                'inventorytransfer_id' => $transferId,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'product_sku' => $item['sku'],
                'qty_transferred' => isset($item['qty_transferred']) ? $item['qty_transferred'] : 0,
                'qty_received' => isset($item['qty_received']) ? $item['qty_received'] : 0
            ];
            $transferProduct->setData($data)->save();

            $totalQtyToTransfer += (float)$item['qty_transferred'];
        }

        $transferInventory->setQtyTransferred($totalQtyToTransfer)->save();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getSelectBarcodeProductListJson($productIds = []){
        $result = [];
        $collection = $this->getProductCollection();
        if(count($productIds)) {
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
        }
        if (!$collection->isLoaded()) {
            $collection->load();
        }
        $items = $collection->toArray();

        foreach ($items as $item) {
            if (isset($item['barcode'])) {
                $barcodes = explode(',', (string)$item['barcode']);
                foreach ($barcodes as $barcode) {
                    $result[$barcode] = $item;
                }
            }
        }
        // set image url
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $path = $storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        $productTypes = $this->type->getOptionArray();

        /** @var \Magento\Catalog\Helper\Image $imageCalalogHelper */
        $imageCalalogHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Image::class);
        foreach ($result as &$item) {
            if (isset($item['thumbnail'])) {
                if($item['thumbnail'] != 'no_selection') {
                    $item['thumbnail_src'] = $path . 'catalog/product' . $item['thumbnail'];
                } else {
                    $item['thumbnail_src'] = $imageCalalogHelper->getDefaultPlaceholderUrl('thumbnail');
                }
            }
            if (isset($item['type_id'])) {
                $item['type'] = isset($productTypes[$item['type_id']]) ? $productTypes[$item['type_id']] : $item['type_id'];
            }
            if (isset($item['quantity'])) {
                $item['quantity'] = (float)$item['quantity'];
            }
        }

        return $this->_objectManager
            ->create('Magento\Framework\Json\EncoderInterface')
            ->encode($result);
    }

    /**
     * @inheritDoc
     */
    public function getSelectBarcodeReceivingProductListJson($transferId) {
        /** @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\Collection $collection */
        $collection = $this->inventoryTransferProductCollectionFactory->create();
        $collection->getTransferedProducts($transferId);
        $collection->getSelect()->where('((qty_transferred - qty_received > 0) OR (qty_transferred = 0))');

        $result = [];
        if (!$collection->isLoaded()) {
            $collection->load();
        }

        $items = $collection->getData();

        /** @var \Magento\Catalog\Helper\Image $imageCalalogHelper */
        $imageCalalogHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Image::class);

        $productTypes = $this->type->getOptionArray();

        foreach ($items as $item) {
            if (isset($item['image_url']) && strpos($item['image_url'], 'no_selection') !== false) {
                $item['image_url'] = $imageCalalogHelper->getDefaultPlaceholderUrl('thumbnail');
            }
            if (isset($item['barcode'])) {
                $barcodes = explode(',', (string)$item['barcode']);
                foreach ($barcodes as $barcode) {
                    $result[$barcode] = $item;
                }
            }
        }

        foreach ($result as &$item) {
            if (isset($item['type_id'])) {
                $item['type'] = isset($productTypes[$item['type_id']]) ? $productTypes[$item['type_id']] : $item['type_id'];
            }

            $item['available_qty_to_receive'] = (float)$item['available_qty_to_receive'];
        }

        return $this->_objectManager
            ->create('Magento\Framework\Json\EncoderInterface')
            ->encode($result);
    }

    /**
     * @inheritdoc
     */
    public function getProductCollection()
    {
        $collection = $this->_objectManager->create('Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\GlobalStock\Collection');

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function addProductsToInventoryTransfer($transferId, $items) {
        $products = $this->getProductFromInventoryTransfer($transferId);
        // get list current products
        $currentProducts = [];
        foreach ($products as $product) {
            $currentProducts[$product->getProductSku()] = $product;
        }

        // Add & update products
        /** @var InventoryTransferInterface $transferInventory */
        $transferInventory = $this->inventoryTransferFactory->create()->load($transferId);
        $totalQtyToTransfer = $transferInventory->getQtyTransferred();
        foreach ($items as $item) {
            // Update existing product
            if(isset($currentProducts[$item['sku']])) {
                /** @var \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterface $currentItem */
                $currentItem = $currentProducts[$item['sku']];
                $newQtyToTransfer = isset($item['qty_transferred']) ? $item['qty_transferred'] : 0;
                $totalQtyToTransfer = $totalQtyToTransfer - (float)$currentItem->getQtyTransferred() + (float)$newQtyToTransfer;
                $currentItem->setQtyTransferred($newQtyToTransfer)->save();
                continue;
            }

            // Add new product item
            /** @var \Magestore\TransferStock\Api\Data\InventoryTransfer\InventoryTransferProductInterface $transferProduct */
            $transferProduct = $this->inventoryTransferProductFactory->create();
            $data = [
                'inventorytransfer_id' => $transferId,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'product_sku' => $item['sku'],
                'qty_transferred' => isset($item['qty_transferred']) ? $item['qty_transferred'] : 0,
                'qty_received' => isset($item['qty_received']) ? $item['qty_received'] : 0
            ];
            $transferProduct->setData($data)->save();

            $totalQtyToTransfer += (float)$item['qty_transferred'];
        }

        $transferInventory->setQtyTransferred($totalQtyToTransfer)->save();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function receiveProducts($transferId, $items) {
        $receiveItemsData = [];
        $totalReceiveQty = 0;

        foreach ($items as $item) {
            $totalReceiveQty += $item['qty'];
            $receiveItemsData[$item['product_sku']] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'],
                'qty' => $item['qty']
            ];
        }

        /** @var \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveInterface $receive */
        $receive = $this->receiveInterfaceFactory->create();
        $receive->setCreatedBy($this->authSession->getUser()->getUserName());
        $receive->setInventorytransferId($transferId);
        $receive->setTotalQty($totalReceiveQty);
        $receive->save();

        foreach ($receiveItemsData as $itemDatum) {
            /** @var \Magestore\TransferStock\Api\Data\InventoryTransfer\ReceiveProductInterface $receiveItem */
            $receiveItem = $this->receiveProductInterfaceFactory->create();
            $receiveItem->setData($itemDatum);
            $receiveItem->setReceiveId($receive->getReceiveId());
            $receiveItem->save();
        }

        $this->increaseProductQtyInDestinationSource($transferId, $receiveItemsData);

        $this->updateQuantityInTransferStock($transferId, $receiveItemsData, $totalReceiveQty);

        return true;
    }

    /**
     * @param int $transferId
     * @param array $receiveItemsData
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function increaseProductQtyInDestinationSource($transferId, $receiveItemsData) {
        /** @var InventoryTransferInterface $transferInventory */
        $transferInventory = $this->inventoryTransferFactory->create()->load($transferId);

        $receiveItems = [];
        foreach ($receiveItemsData as $sku => $item) {
            $sourceItems = $this->sourceManagement->getSourceItemsMap($sku, [$transferInventory->getDesWarehouseCode()]);
            $oldQty = isset($sourceItems[$transferInventory->getDesWarehouseCode()]) ? $sourceItems[$transferInventory->getDesWarehouseCode()]->getQuantity() : 0;
            $receiveItems[$sku] = $oldQty + (float)$item['qty'];
        }

        $this->processChangeQuantity($transferInventory->getDesWarehouseCode(), $receiveItems);
        return true;
    }

    /**
     * @param int $transferId
     * @param array $receiveItemsData
     * @param float $totalReceiveQty
     */
    public function updateQuantityInTransferStock($transferId, $receiveItemsData, $totalReceiveQty) {
        $items = $this->getProductFromInventoryTransfer($transferId);
        $numberOfReceived = $this->inventoryReceiveProductCollectionFactory->create()->getNumberProductSkuReceive($transferId);
        /** @var InventoryTransferInterface $transferInventory */
        $transferInventory = $this->inventoryTransferFactory->create()->load($transferId);

        $transferInventory->setQtyReceived($transferInventory->getQtyReceived() + $totalReceiveQty);
        if($transferInventory->getQtyReceived() >= $transferInventory->getQtyTransferred() && $numberOfReceived >= $items->getSize()) {
            $transferInventory->setStatus(Status::STATUS_CLOSED);
            $transferInventory->setStage(Stage::STAGE_COMPLETED);
        } else {
            $transferInventory->setStage(Stage::STAGE_RECEIVING);
        }
        $transferInventory->save();

        foreach ($items as $item) {
            if(isset($receiveItemsData[$item->getProductSku()])) {
                $item->setQtyReceived($item->getQtyReceived() + $receiveItemsData[$item->getProductSku()]['qty'])->save();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function receiveAllProducts($transferId) {
        $items = $this->getProductFromInventoryTransfer($transferId);

        $dataReceive = [];
        foreach ($items as $item) {
            $dataReceive[] = [
                'product_id' => $item->getProductId(),
                'product_sku' => $item->getProductSku(),
                'product_name' => $item->getProductName(),
                'qty' => $item->getQtyTransferred() - $item->getQtyReceived()
            ];
        }

        return $this->receiveProducts($transferId, $dataReceive);
     }
}
