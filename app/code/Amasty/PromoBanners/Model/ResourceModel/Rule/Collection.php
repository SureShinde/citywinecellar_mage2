<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Model\ResourceModel\Rule;

use Amasty\PromoBanners\Model\Rule;

/**
 * @method Rule[] getItems()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            \Amasty\PromoBanners\Model\Rule::class,
            \Amasty\PromoBanners\Model\ResourceModel\Rule::class
        );
    }

    public function addStoreFilter($storeIds, $withAll = true)
    {
        $condition = [];
        $field = [];

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        if ($withAll) {
            $condition[] = ['eq' => ''];
            $field[] = 'stores';
            $condition[] = ['eq' => \Magento\Cms\Ui\Component\Listing\Column\Cms\Options::ALL_STORE_VIEWS];
            $field[] = 'stores';
        }

        foreach ($storeIds as $storeId) {
            $condition[] = ['finset' => $storeId];
            $field[] = 'stores';
        }

        if (!empty($field)) {
            $this->addFieldToFilter($field, $condition);
        }

        return $this;
    }
}
