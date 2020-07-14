<?php

namespace Magestore\Webpos\Observer\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ProductAttributeUpdate implements ObserverInterface
{
    /**
     * @var \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface
     */
    protected $productDeletedRepository;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResouce;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    public function __construct(
        \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface $productDeletedRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ResourceModel\Product $productResouce,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->productDeletedRepository = $productDeletedRepository;
        $this->resourceConnection = $resourceConnection;
        $this->productResouce = $productResouce;
        $this->productFactory = $productFactory;
    }

    public function execute(EventObserver $observer)
    {
        $productIds = $observer->getProductIds();
        $attributesData = $observer->getAttributesData();
        if ((isset($attributesData['status']) && $attributesData['status'] == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
            || (isset($attributesData['webpos_visible']) && $attributesData['webpos_visible'] != \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::VISIBLE_ON_WEBPOS)
        ) {
            foreach ($productIds as $productId) {
                $this->productDeletedRepository->insertByProductId($productId);
            }
        } else if ((isset($attributesData['status']) && $attributesData['status'] == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            || (isset($attributesData['webpos_visible']) && $attributesData['webpos_visible'] == \Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection::VISIBLE_ON_WEBPOS)
        ) {
            $select = $this->productResouce->getConnection()->select()->from(
                $this->productResouce->getTable('catalog_product_entity'),
                ['sku', 'entity_id', 'type_id']
            )->where(
                'entity_id IN (?)',
                $productIds
            );
            $productSkus = $this->productResouce->getConnection()->fetchAll($select);
            foreach ($productSkus as $productSku) {
                if (in_array($productSku['entity_id'], $productIds)) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $this->productFactory->create();
                    $product->setId($productSku['entity_id']);
                    $product->setEntityId($productSku['entity_id']);
                    $product->setSku($productSku['sku']);
                    $this->productDeletedRepository->deleteByProduct($product);
                }
            }
            /*foreach ($productIds as $productId) {
                $this->productDeletedRepository->deleteByProductId($productId);
            }*/
        }
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('catalog_product_entity'); //gives table name with prefix
        try {
            $connection->update(
                $tableName,
                ['updated_at' => date('Y-m-d H:i:s')],
                $connection->quoteInto("entity_id IN (?)", $productIds)
            );
        } catch (\Exception $e) {
            $productIds = $observer->getProductIds();
        }
    }
}