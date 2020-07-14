<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Abstracts;

use Amasty\Blog\Model\ResourceModel\Traits\CollectionTrait;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    use CollectionTrait;

    /**
     * @var bool
     */
    private $isStoreDataAdded = false;

    /**
     * @var array
     */
    private $storeIds;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeLoad()
    {
        $this->applyStoreFilter();

        return parent::_beforeLoad();
    }

    /**
     * @param string $field
     * @param string $direction
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == "stores") {
            $this->addStoreData()->getSelect()->order('store.store_id ' . $direction);
        } else {
            return parent::setOrder($field, $direction);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addStoreData()
    {
        if ($this->isStoreDataAdded()) {
            return $this;
        }

        $this->setIsStoreDataAdded(true);
        $table = $this->getMainTable() . "_store";
        $idFieldName = $this->getResource()->getIdFieldName();

        $this->getSelect()
            ->joinInner(['store' => $table], 'store.' . $idFieldName . ' = main_table.' . $idFieldName, [])
            ->columns('store.*')
            ->group('main_table.' . $idFieldName);

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function applyStoreFilter()
    {
        if ($this->storeIds) {
            $this->addStoreData();
            $store = $this->storeIds;

            if (!is_array($store)) {
                $store = [$store];
            }

            $storesFilter = "'" . implode("','", $store) . "'";
            $this->getSelect()->where('store.store_id IN (' . $storesFilter . ')');
        }

        return $this;
    }

    /**
     * @param $store
     *
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if (is_array($store)) {
            array_push($store, \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            $this->storeIds = $store;
        } else {
            $this->storeIds = [$store, \Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getQueryText()
    {
        return $this->queryText;
    }

    /**
     * @param $queryText
     * @return $this
     */
    public function setQueryText($queryText)
    {
        $this->queryText = $queryText;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStoreDataAdded()
    {
        return $this->isStoreDataAdded;
    }

    /**
     * @param $storeData
     * @return $this
     */
    public function setIsStoreDataAdded($isStoreDataAdded)
    {
        $this->isStoreDataAdded = $isStoreDataAdded;

        return $this;
    }

    /**
     * @return null
     */
    public function getStoreTable()
    {
        return null;
    }

    /**
     * @param string $column
     * @return string
     */
    public function getStoreColumn($column)
    {
        return $column;
    }
}
