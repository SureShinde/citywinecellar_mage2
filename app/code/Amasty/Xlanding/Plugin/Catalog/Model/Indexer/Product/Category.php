<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalog\Model\Indexer\Product;

use Magento\Catalog\Model\Indexer\Product\Category as ProductCategoryIndexer;

class Category
{
    /**
     * @var \Amasty\Xlanding\Model\Indexer\Catalog\Category\Product
     */
    private $dynamicCategoriesIndexer;

    /**
     * @var array
     */
    private $entityIds = [];

    public function __construct(\Amasty\Xlanding\Model\Indexer\Catalog\Category\Product $dynamicCategoriesIndexer)
    {
        $this->dynamicCategoriesIndexer = $dynamicCategoriesIndexer;
    }

    /**
     * @param ProductCategoryIndexer $indexer
     * @param $ids
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(ProductCategoryIndexer $indexer, $ids)
    {
        $this->entityIds = $ids;
        return [$ids];
    }

    /**
     * @param ProductCategoryIndexer $indexer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(ProductCategoryIndexer $indexer)
    {
        $this->dynamicCategoriesIndexer->executeProducts($this->entityIds);
    }

    /**
     * @param ProductCategoryIndexer $indexer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecuteFull(ProductCategoryIndexer $indexer)
    {
        $this->dynamicCategoriesIndexer->executeFull();
    }
}
