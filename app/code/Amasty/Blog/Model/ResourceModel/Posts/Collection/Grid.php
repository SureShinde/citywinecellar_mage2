<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Posts\Collection;

use Amasty\Blog\Model\ResourceModel\Posts\Collection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;

/**
 * Class
 */
class Grid extends \Amasty\Blog\Model\ResourceModel\Posts\Collection implements SearchResultInterface
{
    protected $_map = [
        'fields' => [
            'url_key' => 'main_table.url_key',
            'title' => 'main_table.title',
            'post_id' => 'main_table.post_id'
        ]
    ];

    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * Initialize db select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->addStores();
        $this->addCategories();
        $this->getSelect()
            ->group('main_table.post_id')
            ->columns('GROUP_CONCAT(store_id SEPARATOR ",") as store_id')
            ->columns('GROUP_CONCAT(categories.category_id SEPARATOR ",") as category_id');
        parent::_initSelect();

        return $this;
    }

    /**
     * @param $categoryIds
     *
     * @return $this
     */
    public function addCategoryFilter($categoryIds)
    {
        $categoryIds = is_array($categoryIds) ? $categoryIds : [$categoryIds];
        $this->getSelect()->where('categories.category_id IN (?)', $categoryIds);

        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\Search\DocumentInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @return \Magento\Framework\Api\Search\AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     *
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\Search\SearchCriteriaInterface
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }
}
