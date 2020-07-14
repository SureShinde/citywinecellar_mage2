<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product\Sorting;

use \Magento\Catalog\Model\ResourceModel\Product\Collection;

class NameAscending extends SortAbstract implements SortInterface
{
    /**
     * @return string
     */
    protected function getSortField()
    {
        return 'name';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __("Name: Ascending");
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection) {
        parent::sort($collection);
        $collection->addAttributeToSelect($this->getSortField(), true);
        $collection->addOrder($this->getSortField(), $this->ascOrder());
        return $collection;
    }
}
