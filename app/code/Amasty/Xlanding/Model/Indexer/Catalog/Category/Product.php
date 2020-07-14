<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Indexer\Catalog\Category;

use Amasty\Xlanding\Api\Data\PageInterface;
use Amasty\Xlanding\Model\Indexer\Catalog\Category\Product\TableResolver;
use Magento\Framework\EntityManager\MetadataPool;

class Product
{
    /**
     * Catalog category index table name
     */
    const MAIN_INDEX_TABLE = 'catalog_category_product_index';

    /**
     * Select array for dynamic categories
     *
     * @var \Magento\Framework\DB\Select[]
     */
    protected $selects = [];

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var array
     */
    private $categoryPath = [];

    /**
     * @var array
     */
    private $productIds = [];

    /**
     * @var array
     */
    private $categoryIds = [];

    /**
     * @var TableResolver
     */
    private $tableResolver;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $config,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        TableResolver $tableResolver
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->tableResolver = $tableResolver;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param array $productIds
     * @return $this
     */
    public function setProductIds($productIds = [])
    {
        $this->productIds = $productIds;
        return $this;
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function setCategoryIds($categoryIds = [])
    {
        $this->categoryIds = $categoryIds;
        return $this;
    }

    /**
     * @return $this
     */
    public function executeFull()
    {
        $this->removeCategories();
        $this->reindex();
        return $this;
    }

    /**
     * @param array $productIds
     * @return $this
     */
    public function executeProducts($productIds = [])
    {
        $this->removeProducts($productIds);
        $this->setProductIds($productIds);
        $this->reindex();

        return $this;
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function executeCategories($categoryIds = [])
    {
        $this->removeCategories($categoryIds);
        $this->setCategoryIds($categoryIds);
        $this->reindex();

        return $this;
    }

    /**
     * @param array $productIds
     * @return $this
     */
    private function removeProducts($productIds = [])
    {
        if (!empty($productIds)) {
            foreach ($this->storeManager->getStores() as $store) {
                $this->connection->delete(
                    $this->getMainTable($store->getId()),
                    ['product_id IN (?)' => $productIds , 'store_id = ?' => $store->getId()]
                );
            }
        }
        return $this;
    }

    /**
     * Remove index entries before reindexation
     *
     * @return $this
     */
    private function removeCategories($categoryIds = [])
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToFilter(PageInterface::IS_CATEGORY_DYNAMIC, 1)
            ->addAttributeToSelect(PageInterface::DYNAMIC_CATEGORY_PAGE_ID, true);
        if (!empty($categoryIds)) {
            $collection->addIdFilter($categoryIds);
        }

        $categoryIds = $collection->getAllIds();

        foreach ($this->storeManager->getStores() as $store) {
            $this->connection->delete(
                $this->getMainTable($store->getId()),
                ['category_id IN (?)' => $categoryIds, 'store_id = ?' => $store->getId()]
            );
        }

        return $this;
    }

    /**
     * @param int $storeId
     * @return string
     */
    private function getMainTable($storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID)
    {
        return $this->tableResolver->getTableName($storeId);
    }

    /**
     * @return $this
     */
    private function reindex()
    {
        foreach ($this->storeManager->getStores() as $store) {
            $this->reindexDynamicCategories($store);
        }

        return $this;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @return $this
     */
    private function reindexDynamicCategories(\Magento\Store\Model\Store $store)
    {
        $select = $this->getDynamicCategoriesSelect($store);
        $this->connection->query(
            $this->connection->insertFromSelect(
                $select,
                $this->getMainTable($store->getId()),
                ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            )
        );

        return $this;
    }

    /**
     * @param $categoryId
     * @return string
     */
    private function getPathFromCategoryId($categoryId)
    {
        if (!isset($this->categoryPath[$categoryId])) {
            $this->categoryPath[$categoryId] = $this->connection->fetchOne(
                $this->connection->select()->from(
                    $this->getTable('catalog_category_entity'),
                    ['path']
                )->where(
                    'entity_id = ?',
                    $categoryId
                )
            );
        }
        return $this->categoryPath[$categoryId];
    }

    /**
     * Retrieve select for reindex products of non anchor categories
     *
     * @param \Magento\Store\Model\Store $store
     * @return \Magento\Framework\DB\Select
     */
    protected function getDynamicCategoriesSelect(\Magento\Store\Model\Store $store)
    {
        if (!isset($this->selects[$store->getId()])) {
            $statusAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'status'
            )->getId();
            $visibilityAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'visibility'
            )->getId();

            $isCategoryDynamicAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                PageInterface::IS_CATEGORY_DYNAMIC
            )->getId();

            $dynamicCategoryPageIdAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                PageInterface::DYNAMIC_CATEGORY_PAGE_ID
            )->getId();

            $rootPath = $this->getPathFromCategoryId($store->getRootCategoryId());

            $metadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
            $linkField = $metadata->getLinkField();
            $select = $this->connection->select()->from(
                ['cc' => $this->getTable('catalog_category_entity')],
                []
            )->joinInner(
                ['ccii' => $this->getTable('catalog_category_entity_int')],
                'ccii.' . $linkField . ' = cc.entity_id AND ccii.store_id = 0 AND ccii.attribute_id = '
                    . $isCategoryDynamicAttributeId .' AND ccii.value = 1',
                []
            )->joinInner(
                ['ccipid' => $this->getTable('catalog_category_entity_int')],
                'ccipid.' . $linkField . ' = cc.entity_id AND ccipid.store_id = 0 AND ccipid.attribute_id = '
                . $dynamicCategoryPageIdAttributeId,
                []
            )->joinInner(
                ['lp' => $this->getTable('amasty_xlanding_page')],
                'lp.page_id = ccipid.value AND lp.is_active = ' . \Amasty\Xlanding\Model\Page::STATUS_DYNAMIC,
                []
            )->joinInner(
                ['ccp' => $this->getTable('amasty_xlanding_page_product_index')],
                'ccp.page_id = ccipid.value AND ccp.store_id = ' . $store->getId(),
                []
            )->joinInner(
                ['cpw' => $this->getTable('catalog_product_website')],
                'cpw.product_id = ccp.product_id',
                []
            )->joinInner(
                ['cpe' => $this->getTable('catalog_product_entity')],
                'ccp.product_id = cpe.entity_id',
                []
            )->joinInner(
                ['cpsd' => $this->getTable('catalog_product_entity_int')],
                'cpsd.' . $linkField . ' = cpe.' . $linkField . ' AND cpsd.store_id = 0' .
                ' AND cpsd.attribute_id = ' .
                $statusAttributeId,
                []
            )->joinLeft(
                ['cpss' => $this->getTable('catalog_product_entity_int')],
                'cpss.' . $linkField . ' = cpe.' . $linkField . ' AND cpss.attribute_id = cpsd.attribute_id' .
                ' AND cpss.store_id = ' .
                $store->getId(),
                []
            )->joinInner(
                ['cpvd' => $this->getTable('catalog_product_entity_int')],
                'cpvd.' . $linkField . ' = cpe.' . $linkField . ' AND cpvd.store_id = 0' .
                ' AND cpvd.attribute_id = ' .
                $visibilityAttributeId,
                []
            )->joinLeft(
                ['cpvs' => $this->getTable('catalog_product_entity_int')],
                'cpvs.' . $linkField . ' = cpe.' . $linkField . ' AND cpvs.attribute_id = cpvd.attribute_id' .
                ' AND cpvs.store_id = ' .
                $store->getId(),
                []
            )->where(
                'cc.path LIKE ' . $this->connection->quote($rootPath . '/%')
            )->where(
                'cpw.website_id = ?',
                $store->getWebsiteId()
            )->where(
                $this->connection->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            )->where(
                $this->connection->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
                [
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
                ]
            )->columns(
                [
                    'category_id' => 'cc.entity_id',
                    'product_id' => 'ccp.product_id',
                    'position' => 'ccp.position',
                    'is_parent' => new \Zend_Db_Expr('1'),
                    'store_id' => new \Zend_Db_Expr($store->getId()),
                    'visibility' => new \Zend_Db_Expr(
                        $this->connection->getIfNullSql('cpvs.value', 'cpvd.value')
                    ),
                ]
            );

            if (!empty($this->categoryIds)) {
                $select->where('cc.entity_id IN (?)', $this->categoryIds);
            } elseif (!empty($this->productIds)) {
                $select->where('ccp.product_id IN (?)', $this->productIds);
            }

            $this->selects[$store->getId()] = $select;
        }

        return $this->selects[$store->getId()];
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     */
    private function getMetadataPool()
    {
        return $this->metadataPool;
    }

    /**
     * @param $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        return $this->resource->getTableName($tableName);
    }
}
