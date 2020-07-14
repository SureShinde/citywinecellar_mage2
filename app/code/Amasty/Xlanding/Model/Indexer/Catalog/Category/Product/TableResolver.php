<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Indexer\Catalog\Category\Product;

use \Magento\Framework\Search\Request\Dimension;
use \Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use \Magento\Store\Model\StoreManagerInterface;

class TableResolver
{
    const MAIN_INDEX_TABLE = 'catalog_category_product_index';

    /**
     * @var IndexScopeResolver
     */
    private $tableResolver;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    public function __construct(
        IndexScopeResolver $tableResolver,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->tableResolver = $tableResolver;
        $this->resource = $resource;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTableName($storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID)
    {
        $catalogCategoryProductDimension = new Dimension(
            \Magento\Store\Model\Store::ENTITY,
            $storeId
        );

        $tableName = $this->tableResolver->resolve(
            self::MAIN_INDEX_TABLE,
            [
                $catalogCategoryProductDimension
            ]
        );
        if (!$this->resource->getConnection()->isTableExists($tableName)) {
            $tableName = self::MAIN_INDEX_TABLE;
        }

        return $this->resource->getTableName($tableName);
    }
}
