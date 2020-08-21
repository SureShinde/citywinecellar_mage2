<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\CollectionFactory;

/**
 * Inventory tranfer - Grid shortfall collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShortFall extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct\Collection
     */
    protected $collection;
    /**
     * @var \Magestore\TransferStock\Model\InventoryTransferFactory
     */
    protected $inventoryTransferFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $type;

    /**
     * ShortFall constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CollectionFactory $collectionFactory
     * @param \Magestore\TransferStock\Model\InventoryTransferFactory $inventoryTransferFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\App\RequestInterface $request,
        CollectionFactory $collectionFactory,
        \Magestore\TransferStock\Model\InventoryTransferFactory $inventoryTransferFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product\Type $type,
        $mainTable = 'os_inventorytransfer_product',
        $resourceModel = \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->request = $request;
        $this->collection = $collectionFactory->create();
        $this->inventoryTransferFactory = $inventoryTransferFactory;
        $this->_objectManager = $objectManager;
        $this->type = $type;
        $this->prepareCollection();
    }

    /**
     * Prepare Collection
     *
     * @return ShortFall|void
     */
    public function prepareCollection()
    {
        $inventorytransfer_id = $this->request->getParam('inventorytransfer_id');
        if (!$inventorytransfer_id) {
            return;
        }

        $this->addFieldToFilter('inventorytransfer_id', $inventorytransfer_id);
        $this->getSelect()->where('qty_transferred - qty_received > 0');
        // get image
        $storeManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreManagerInterface::class);
        $path = $storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path .= 'catalog/product';
        $edition = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\ProductMetadataInterface::class)
            ->getEdition();
        $rowId = strtolower($edition) == 'enterprise' ? 'row_id' : 'entity_id';
        /* @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute */
        $eavAttribute = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute::class);
        $productImagesAttributeId = $eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'thumbnail');
        $this->getSelect()->joinLeft(
            ['catalog_product_entity_varchar_img' => $this->getTable('catalog_product_entity_varchar')],
            "main_table.product_id = catalog_product_entity_varchar_img.$rowId && 
                catalog_product_entity_varchar_img.attribute_id = $productImagesAttributeId && 
                catalog_product_entity_varchar_img.store_id = 0",
            ['']
        )->columns([
            'image' => 'catalog_product_entity_varchar_img.value',
            'image_url' => 'CONCAT("'.$path.'", catalog_product_entity_varchar_img.value)'
        ]);

        $resource = $this->getResource();

        $productTable = $resource->getTable('catalog_product_entity');
        $this->getSelect()->joinLeft(
            ['product' => $productTable],
            "main_table.product_id = product.entity_id",
            ['type_id']
        );

        $this->getSelect()->group('main_table.product_sku');
        $this->getSelect()->columns(['qty_not_received' => new \Zend_Db_Expr('qty_transferred - qty_received')]);

        $collection = $this->setOrder('entity_id', 'DESC');
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = parent::getData();
        if ($this->request->getParam('inventorytransfer_id')) {
            /** @var \Magento\Catalog\Helper\Image $imageCalalogHelper */
            $imageCalalogHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Image::class);
            $productTypes = $this->type->getOptionArray();
            foreach ($data as &$item) {
                if (isset($productTypes[$item['type_id']])) {
                    $item['type_id'] = $productTypes[$item['type_id']];
                }
                $item['qty_transferred'] = (float)$item['qty_transferred'];
                $item['qty_received'] = (float)$item['qty_received'];
                $item['qty_not_received'] = (float)$item['qty_not_received'];
                if (isset($item['image_url']) && strpos($item['image_url'], 'no_selection') !== false) {
                    $item['image_url'] = $imageCalalogHelper->getDefaultPlaceholderUrl('thumbnail');
                }
            }
        }
        return $data;
    }
}
