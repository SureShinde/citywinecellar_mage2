<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product\Sorting;

use \Magento\Catalog\Model\ResourceModel\Product\Collection;

class NewestTop extends SortAbstract implements SortInterface
{
    /**
     * @return string
     */
    protected function getSortField()
    {
        return 'entity_id';
    }

    /**
     * @return string
     */
    protected function getSortDirection()
    {
        return $this->descOrder();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __("Newest products first");
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection)
    {
        parent::sort($collection);
        $collection->addOrder($this->getSortField(), $this->getSortDirection());
        return $collection;
    }
}
