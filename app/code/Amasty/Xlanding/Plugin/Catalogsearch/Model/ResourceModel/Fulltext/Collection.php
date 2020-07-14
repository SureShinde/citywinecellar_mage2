<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalogsearch\Model\ResourceModel\Fulltext;

use \Magento\Eav\Model\Entity\Collection\AbstractCollection;

class Collection
{
    const POSITION_COLUMN_NAME = 'cat_index_position';

    const COLUMN_NAME_INDEX = 2;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var bool
     */
    private $isPositionIndexJoinApplied = false;

    /**
     * @var \Amasty\Base\Model\MagentoVersion
     */
    private $magentoVersion;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\Base\Model\MagentoVersion $magentoVersion
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function afterAddCategoryFilter($collection)
    {
        if ($page = $this->coreRegistry->registry('amasty_xlanding_page')) {
            $this->addPositionIndexJoin($collection);
        }

        return $collection;
    }

    /**
     * @param $collection
     * @return $this
     */
    private function addPositionIndexJoin($collection)
    {
        if (!$this->isPositionIndexJoinApplied) {
            /**
             * @var \Magento\Framework\DB\Select $select
             */
            $select = $collection->getSelect();
            $page = $this->coreRegistry->registry('amasty_xlanding_page');
            $columns = $select->getPart(\Magento\Framework\DB\Select::COLUMNS);
            foreach ($columns as $index => $column) {
                if (isset($column[self::COLUMN_NAME_INDEX])
                    && $column[self::COLUMN_NAME_INDEX] == self::POSITION_COLUMN_NAME
                ) {
                    unset($columns[$index]);
                    break;
                }
            }
            $select->setPart(\Magento\Framework\DB\Select::COLUMNS, $columns);
            $table = 'amasty_xlanding_page_product_index';

            if (version_compare($this->magentoVersion->get(), '2.3.2', '>=')) {
                $positionExpr = new \Zend_Db_Expr("IFNULL(page_product_index.position, 0)");
            } else {
                $positionExpr = new \Zend_Db_Expr("IFNULL(page_product_index.position, cat_index.position)");
            }

            $select->joinInner(
                ['page_product_index' => $collection->getResource()->getTable($table)],
                'page_product_index.product_id = e.entity_id'
                . ' AND page_product_index.page_id = ' . $page->getId()
                . ' AND page_product_index.store_id = ' . $collection->getStoreId(),
                ['cat_index_position' => $positionExpr]
            );
            $this->isPositionIndexJoinApplied = true;
        }

        return $this;
    }

    /**
     * @param $collection
     * @param callable $proceed
     * @param $attribute
     * @param $dir
     * @return mixed
     */
    public function aroundAddAttributeToSort(
        $collection,
        callable $proceed,
        $attribute,
        $dir = AbstractCollection::SORT_ORDER_ASC
    ) {
        if ($this->coreRegistry->registry('amasty_xlanding_page') && $attribute == 'position') {
            $this->addPositionIndexJoin($collection);
            $collection->getSelect()->order("cat_index_position {$dir}");
            return $collection;
        }

        return $proceed($attribute, $dir);
    }
}
