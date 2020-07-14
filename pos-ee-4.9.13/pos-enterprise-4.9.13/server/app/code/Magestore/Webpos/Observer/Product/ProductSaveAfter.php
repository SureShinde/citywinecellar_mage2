<?php

namespace Magestore\Webpos\Observer\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magestore\Webpos\Model\ResourceModel\Catalog\Product\Collection;

/**
 * Class ProductSaveAfter
 *
 * @package Magestore\Webpos\Observer\Product
 */
class ProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface
     */
    protected $productDeletedRepository;
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $productType;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $productTypes = [];

    /**
     * ProductSaveAfter constructor.
     *
     * @param \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface $productDeletedRepository
     * @param \Magento\Catalog\Model\Product\Type $productType
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magestore\Webpos\Api\Log\ProductDeletedRepositoryInterface $productDeletedRepository,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->productDeletedRepository = $productDeletedRepository;
        $this->productType = $productType;
        $this->productMetadata = $productMetadata;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Execute observer
     *
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $product = $observer->getProduct();
        $productId = $product->getId();

        if (version_compare($this->productMetadata->getVersion(), '2.1.13', '<=')) {
            try {
                $connection = $this->resourceConnection->getConnection();
                //gives table name with prefix
                $tableName = $this->resourceConnection->getTableName('catalog_product_entity');
                $connection->update(
                    $tableName,
                    ['updated_at' => date('Y-m-d H:i:s')],
                    $connection->quoteInto("entity_id = ?", $productId)
                );
            } catch (\Exception $e) {
                $productId = $product->getId();
            }
        }
        if (!$product->isComposite()) {
            $parentIds = [];
            foreach ($this->getProductTypeInstances() as $typeInstance) {
                /* @var $typeInstance \Magento\Catalog\Model\Product\Type\AbstractType */
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                $parentIds = array_merge($parentIds, $typeInstance->getParentIdsByChild($productId));
            }
            if (!empty($parentIds)) {
                $connection = $this->resourceConnection->getConnection();
                $tableName = $this->resourceConnection->getTableName('catalog_product_entity');
                $connection->update(
                    $tableName,
                    ['updated_at' => date("Y-m-d H:i:s")],
                    $connection->quoteInto("entity_id IN (?)", $parentIds)
                );
            }
        }

        if (!$product->isObjectNew()) {
            if ($product->getStatus() == Status::STATUS_DISABLED
                || $product->getData('webpos_visible') != Collection::VISIBLE_ON_WEBPOS
            ) {
                if ($productId) {
                    $this->productDeletedRepository->insertByProductId($productId);
                }
            } elseif ($product->getStatus() == Status::STATUS_ENABLED
                && $product->getData('webpos_visible') == Collection::VISIBLE_ON_WEBPOS
            ) {
                $this->productDeletedRepository->deleteByProduct($product);
            }
        }
    }

    /**
     * Retrieve Product Type Instances
     *
     * @return array
     */
    public function getProductTypeInstances()
    {
        if (empty($this->productTypes)) {
            $productEmulator = new \Magento\Framework\DataObject();
            foreach (array_keys($this->productType->getTypes()) as $typeId) {
                $productEmulator->setTypeId($typeId);
                $this->productTypes[$typeId] = $this->productType->factory($productEmulator);
            }
        }
        return $this->productTypes;
    }
}
