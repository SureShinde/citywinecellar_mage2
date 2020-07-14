<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product\Sorting;

use \Magento\Catalog\Model\ResourceModel\Product\Collection;

class UserDefined extends SortAbstract implements SortInterface
{
    /**
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection)
    {
        return parent::sort($collection);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __("None");
    }
}
