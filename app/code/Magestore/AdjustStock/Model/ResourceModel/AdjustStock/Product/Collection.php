<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product;

/**
 * Class Collection
 *
 * Product collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magestore\AdjustStock\Model\AdjustStock\Product::class,
            \Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Product::class
        );
    }

    /**
     * Get adjusted product
     *
     * @param int $adjustStockId
     * @return Collection
     */
    public function getAdjustedProducts($adjustStockId)
    {
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
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute */
        $eavAttribute = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute::class);
        $productImagesAttributeId = $eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'image');
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

        $collection = $this->addFieldToFilter('adjuststock_id', $adjustStockId)
                           ->setOrder('product_id', 'DESC');
        return $collection;
    }
}
