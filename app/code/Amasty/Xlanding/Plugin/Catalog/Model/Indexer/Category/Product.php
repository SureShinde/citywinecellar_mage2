<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalog\Model\Indexer\Category;

use Magento\Catalog\Model\Indexer\Category\Product as CategoryProductIndexer;

class Product
{
    /**
     * @var \Amasty\Xlanding\Model\Indexer\Catalog\Category\Product
     */
    private $dynamicCategoriesIndexer;

    /**
     * @var array
     */
    private $entityIds = [];

    public function __construct(
        \Amasty\Xlanding\Model\Indexer\Catalog\Category\Product $dynamicCategoriesIndexer
    ) {
        $this->dynamicCategoriesIndexer = $dynamicCategoriesIndexer;
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @param $ids
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(CategoryProductIndexer $indexer, $ids)
    {
        $this->entityIds = $ids;
        return [$ids];
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(CategoryProductIndexer $indexer)
    {
        $this->dynamicCategoriesIndexer->executeCategories($this->entityIds);
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @param $ids
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteRow(CategoryProductIndexer $indexer, $id)
    {
        $this->entityIds = [$id];
        return [$id];
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecuteRow(CategoryProductIndexer $indexer)
    {
        $this->dynamicCategoriesIndexer->executeCategories($this->entityIds);
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @param $ids
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteList(CategoryProductIndexer $indexer, $ids)
    {
        $this->entityIds = $ids;
        return [$ids];
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecuteList(CategoryProductIndexer $indexer)
    {
        $this->dynamicCategoriesIndexer->executeCategories($this->entityIds);
    }

    /**
     * @param CategoryProductIndexer $indexer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecuteFull(CategoryProductIndexer $indexer)
    {
        $this->dynamicCategoriesIndexer->executeFull();
    }
}
