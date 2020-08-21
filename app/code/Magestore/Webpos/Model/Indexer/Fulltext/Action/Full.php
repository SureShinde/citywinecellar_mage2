<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Fulltext\Action;

use Magento\Framework\App\ResourceConnection;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider;

/**
 * Class provides iterator through number of products suitable for fulltext indexation
 *
 * To be suitable for fulltext index product must meet set of requirements:
 * - to be visible on frontend
 * - to be enabled
 * - in case product is composite at least one sub product must be visible and enabled
 *
 * Follow Magento\CatalogSearch\Model\Indexer\Fulltext\Action\Full
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 * @since 100.0.2
 */
class Full
{
    /**
     * Scope identifier
     */
    const SCOPE_FIELD_NAME = 'scope';

    /**
     * Searchable attributes cache
     *
     * @var \Magento\Eav\Model\Entity\Attribute[]
     */
    protected $searchableAttributes;

    /**
     * Catalog product status
     *
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $catalogProductStatus;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * Batch size for searchable product ids
     *
     * @var int
     */
    private $batchSize;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Full constructor.
     *
     * @param ResourceConnection $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $catalogProductStatus
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param DataProvider $dataProvider
     * @param \Magento\Framework\Registry $registry
     * @param int $batchSize
     */
    public function __construct(
        ResourceConnection $resource,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $catalogProductStatus,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        DataProvider $dataProvider,
        \Magento\Framework\Registry $registry,
        $batchSize = 500
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->eavConfig = $eavConfig;
        $this->catalogProductStatus = $catalogProductStatus;
        $this->eventManager = $eventManager;
        $this->metadataPool = $metadataPool;
        $this->dataProvider = $dataProvider;
        $this->batchSize = $batchSize;
        $this->registry = $registry;
    }

    /**
     * Return validated table name
     *
     * @param string|string[] $table
     * @return string
     */
    protected function getTable($table)
    {
        return $this->resource->getTableName($table);
    }

    /**
     * Regenerate search index for specific store
     *
     * To be suitable for indexation product must meet set of requirements:
     * - to be visible on frontend
     * - to be enabled
     * - in case product is composite at least one sub product must be enabled
     *
     * @param int $storeId Store View Id
     * @param int[] $productIds Product Entity Id
     * @return \Generator
     */
    public function rebuildStoreIndex($storeId, $productIds = null)
    {
        if ($productIds !== null) {
            $productIds = array_unique($productIds);
        }

        // prepare searchable attributes
        $staticFields = [];
        foreach ($this->dataProvider->getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }

        $dynamicFields = $this->getDynamicFields();

        $lastProductId = 0;
        $this->registry->unregister('webpos_productsearch_fulltext');
        $this->registry->register('webpos_productsearch_fulltext', true);
        $products = $this->dataProvider->getSearchableProducts(
            $storeId,
            $staticFields,
            $productIds,
            $lastProductId,
            $this->batchSize
        );

        while (count($products) > 0) {
            $productsIds = array_column($products, 'entity_id');
            $relatedProducts = $this->getRelatedProducts($products);
            $productsIds = array_merge($productsIds, array_values($relatedProducts));

            $productsAttributes = $this->dataProvider->getProductAttributes($storeId, $productsIds, $dynamicFields);

            foreach ($products as $productData) {
                $lastProductId = $productData['entity_id'];

                $productIndex = [$productData['entity_id'] => $productsAttributes[$productData['entity_id']]];
                if (isset($relatedProducts[$productData['entity_id']])) {
                    $childProductsIndex = $this->getChildProductsIndex(
                        $productData['entity_id'],
                        $relatedProducts,
                        $productsAttributes
                    );
                    if (empty($childProductsIndex)) {
                        continue;
                    }
                    $productIndex = $productIndex + $childProductsIndex;
                }

                $index = $this->dataProvider->prepareProductIndex($productIndex, $productData, $storeId);
                yield $productData['entity_id'] => $index;
            }
            $products = $this->dataProvider
                ->getSearchableProducts($storeId, $staticFields, $productIds, $lastProductId, $this->batchSize);
        }
        $this->registry->unregister('webpos_productsearch_fulltext');
    }

    /**
     * Get dynamic attributes for index
     *
     * @return array
     */
    protected function getDynamicFields()
    {
        $dynamicAttributes = [
            'name',
            'webpos_visible',
            'status'
        ];
        $dynamicFields = [];

        foreach ($dynamicAttributes as $attributeCode) {
            $attribute = $this->eavConfig->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeCode
            );
            if (!isset($dynamicFields[$attribute->getBackendType()])) {
                $dynamicFields[$attribute->getBackendType()] = [];
            }
            $dynamicFields[$attribute->getBackendType()][] = $attribute->getAttributeId();
        }

        return $dynamicFields;
    }

    /**
     * Get related (child) products ids
     *
     * Load related (child) products ids for composite product type
     *
     * @param array $products
     * @return array
     */
    private function getRelatedProducts($products)
    {
        $relatedProducts = [];
        foreach ($products as $productData) {
            $relatedProducts[$productData['entity_id']] = $this->dataProvider->getProductChildIds(
                $productData['entity_id'],
                $productData['type_id']
            );
        }
        return array_filter($relatedProducts);
    }

    /**
     * Performs check that product is enabled on Store Front
     *
     * Check that product is enabled on Store Front using status attribute
     * and statuses allowed to be visible on Store Front.
     *
     * @param int $productId
     * @param array $productsAttributes
     * @return bool
     */
    private function isProductEnabled($productId, array $productsAttributes)
    {
        $status = $this->dataProvider->getSearchableAttribute('status');
        $allowedStatuses = $this->catalogProductStatus->getVisibleStatusIds();
        return isset($productsAttributes[$productId][$status->getId()]) &&
            in_array($productsAttributes[$productId][$status->getId()], $allowedStatuses);
    }

    /**
     * Get data for index using related(child) products data
     *
     * Build data for index using child products(if any).
     * Use only enabled child products {@see isProductEnabled}.
     *
     * @param int $parentId
     * @param array $relatedProducts
     * @param array $productsAttributes
     * @return array
     */
    private function getChildProductsIndex($parentId, array $relatedProducts, array $productsAttributes)
    {
        $productIndex = [];
        foreach ($relatedProducts[$parentId] as $productChildId) {
            if ($this->isProductEnabled($productChildId, $productsAttributes)) {
                $productIndex[$productChildId] = $productsAttributes[$productChildId];
            }
        }
        return $productIndex;
    }
}
