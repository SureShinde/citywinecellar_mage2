<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product;

use Amasty\Xlanding\Model\Page\Product\Sorting\Factory as SortingFactory;
use \Amasty\Xlanding\Model\Page\Product\Sorting\SortInterface;
use \Magento\Catalog\Model\ResourceModel\Product\Collection;

class Sorting
{
    /**
     * @var array
     */
    protected $sortMethods = [
        'UserDefined',
        'OutStockBottom',
        'NewestTop',
        'NameAscending',
        'NameDescending',
        'PriceAscending',
        'PriceDescending',
    ];

    /**
     * @var SortingFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $sortInstances = [];

    /**
     * @param SortingFactory $factory
     */
    public function __construct(SortingFactory $factory)
    {
        $this->factory = $factory;
        foreach ($this->sortMethods as $className) {
            $this->sortInstances[] = $this->factory->create($className);
        }
    }

    /**
     * @return array
     */
    public function getSortingOptions()
    {
        $options = [];
        foreach ($this->sortInstances as $idx => $instance) {
            $options[$idx] = $instance->getLabel();
        }
        return $options;
    }

    /**
     * Get the instance of the first option which is None
     *
     * @param int $sortOption
     * @return SortInterface|null
     */
    public function getSortingInstance($sortOption)
    {
        if (isset($this->sortInstances[$sortOption])) {
            return $this->sortInstances[$sortOption];
        }
        return $this->sortInstances[0];
    }

    /**
     * @param Collection $collection
     * @param int $sortingMethod = null
     * @return Collection
     */
    public function applySorting(Collection $collection, $sortingMethod = null)
    {
        $sortBuilder = $this->getSortingInstance($sortingMethod);
        $sortedCollection = $sortBuilder->sort($collection);

        if ($sortedCollection->isLoaded()) {
            $sortedCollection->clear();
        }

        return $sortedCollection;
    }
}
