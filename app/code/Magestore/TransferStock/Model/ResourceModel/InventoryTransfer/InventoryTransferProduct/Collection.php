<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct;

/**
 * Class Collection
 * @package Magestore\TransferStock\Model\ResourceModel\InventoryTransfer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init(
            \Magestore\TransferStock\Model\InventoryTransfer\InventoryTransferProduct::class,
            \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\InventoryTransferProduct::class
        );
    }

    public function addTransferIdToFilter($transferId) {
        $this->addFieldToFilter('inventorytransfer_id', $transferId);
    }

    /**
     * Get transferred product
     *
     * @param $transferId
     * @param string $sendingSourceCode
     * @return Collection
     */
    public function getTransferedProducts($transferId, $sendingSourceCode = '') {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Manager $moduleManager */
        $moduleManager = $om->get('Magento\Framework\Module\Manager');
        $this->addTransferIdToFilter($transferId);
        // get image
        $storeManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Store\Model\StoreManagerInterface');
        $path = $storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        $path .= 'catalog/product';
        $edition = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\ProductMetadataInterface')
            ->getEdition();
        $rowId = strtolower($edition) == 'enterprise' ? 'row_id' : 'entity_id';
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute */
        $eavAttribute = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Eav\Model\ResourceModel\Entity\Attribute');
        $productImagesAttributeId = $eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'thumbnail');
        $this->getSelect()->joinLeft(
            array('catalog_product_entity_varchar_img' => $this->getTable('catalog_product_entity_varchar')),
            "main_table.product_id = catalog_product_entity_varchar_img.$rowId && 
                catalog_product_entity_varchar_img.attribute_id = $productImagesAttributeId && 
                catalog_product_entity_varchar_img.store_id = 0",
            array('')
        )->columns(array(
            'image' => 'catalog_product_entity_varchar_img.value',
            'image_url' => 'CONCAT("'.$path.'", catalog_product_entity_varchar_img.value)'
        ));

        $resource = $this->getResource();
        if ($sendingSourceCode) {
            $sourceItemTable = $resource->getTable('inventory_source_item');
            $this->getSelect()->joinLeft(
                ['current_inventory_source_item' => $sourceItemTable],
                "main_table.product_sku = current_inventory_source_item.sku AND
                current_inventory_source_item.source_code = '$sendingSourceCode'",
                ['quantity']
            );
        }

        $barcodeTable = $resource->getTable('os_barcode');
        if($moduleManager->isEnabled('Magestore_BarcodeSuccess')) {
            $this->getSelect()->joinLeft(
                ['barcode' => $barcodeTable],
                "main_table.product_sku = barcode.product_sku",
                ['barcode']
            );
            $this->getSelect()->columns([
                'barcode' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT barcode.barcode)'),
                'barcode_original_data' => new \Zend_Db_Expr('GROUP_CONCAT(DISTINCT barcode.barcode)')
            ]);
        }

        $productTable = $resource->getTable('catalog_product_entity');
        $this->getSelect()->joinLeft(
            ['product' => $productTable],
            "main_table.product_id = product.entity_id",
            ['type_id']
        );

        $this->getSelect()->group('main_table.product_sku');
        $this->getSelect()->columns([
            'available_qty_to_receive' => new \Zend_Db_Expr('qty_transferred - qty_received'),
        ]);

        $collection = $this->setOrder('entity_id', 'DESC');
        return $collection;
    }
}
